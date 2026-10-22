<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActualizarEstadoRequest;
use App\Http\Requests\Admin\ActualizarRolRequest;
use App\Models\User;
use App\Notifications\AlertaSeguridad;
use App\Services\Auditoria;
use App\Services\ServicioCuentas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Http\Requests\Admin\EliminarUsuarioRequest;
/**
 * 2026-10-22 (J9): audit en cambiarRol, cambiarEstado y destroy.
 *                  Notifica account.suspended al usuario suspendido.
 */
class UsuarioController extends Controller
{
    public function __construct(
        private ServicioCuentas $cuentas,
        private Auditoria $auditoria,
    ) {
    }

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'rol' => ['nullable', 'in:estandar,familiar,administrador'],
            'estado' => ['nullable', 'in:activos,suspendidos'],
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        $usuarios = User::query()
            ->when($filtros['rol'] ?? null, fn($q, $rol) => $q->where('rol', $rol))
            ->when(($filtros['estado'] ?? null) === 'activos', fn($q) => $q->where('activo', true))
            ->when(($filtros['estado'] ?? null) === 'suspendidos', fn($q) => $q->where('activo', false))
            ->when($filtros['buscar'] ?? null, function ($q, $buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('admin.usuarios', compact('usuarios', 'filtros'));
    }

    public function cambiarRol(ActualizarRolRequest $request, User $usuario): RedirectResponse
    {
        $rolAnterior = $usuario->rol;

        $this->cuentas->cambiarRol($usuario, $request->validated('rol'));

        $rolNuevo = $usuario->fresh()->rol;

        $this->auditoria->registrar(
            accion: 'usuario.rol',
            modelo: 'User',
            modeloId: $usuario->id,
            antes: ['rol' => $rolAnterior],
            despues: ['rol' => $rolNuevo],
        );

        return redirect()->route('admin.usuarios')->with('exito', 'Rol actualizado');
    }

    public function cambiarEstado(ActualizarEstadoRequest $request, User $usuario): RedirectResponse
    {
        $activoAntes = (bool) $usuario->activo;
        $activoNuevo = (bool) $request->validated('activo');

        if ($activoNuevo) {
            $this->cuentas->reactivar($usuario);
            $accion = 'usuario.reactivar';
            $mensaje = 'Cuenta activada';
        } else {
            $this->cuentas->suspender($usuario);
            $accion = 'usuario.suspender';
            $mensaje = 'Cuenta suspendida';

            // Notifica al usuario suspendido (al correo actual).
            try {
                $usuario->notify(new AlertaSeguridad('account.suspended', [
                    'fecha' => now()->format('Y-m-d H:i'),
                ]));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar alerta account.suspended', [
                    'user_id' => $usuario->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->auditoria->registrar(
            accion: $accion,
            modelo: 'User',
            modeloId: $usuario->id,
            antes: ['activo' => $activoAntes],
            despues: ['activo' => $activoNuevo],
        );

        return back()->with('exito', $mensaje);
    }

    public function destroy(EliminarUsuarioRequest $request, User $usuario): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $usuario->id) {
            return back()->withErrors([
                'usuario' => 'No puedes eliminar tu propia cuenta desde aquí.',
            ]);
        }

        $idBorrado = $usuario->id;
        $rolBorrado = $usuario->rol;

        $this->cuentas->eliminarCuenta($usuario);

        $this->auditoria->registrar(
            accion: 'usuario.eliminar',
            modelo: 'User',
            modeloId: $idBorrado,
            antes: ['rol' => $rolBorrado],
            despues: null,
        );

        return back()->with('exito', 'Usuario eliminado');
    }
}