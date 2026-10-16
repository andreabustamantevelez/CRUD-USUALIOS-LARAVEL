<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActualizarEstadoRequest;
use App\Http\Requests\Admin\ActualizarRolRequest;
use App\Models\User;
use App\Services\ServicioCuentas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Qué hace: gestión de usuarios y roles desde el panel de administración.
 *           NUNCA devuelve datos financieros: solo nombre, correo, rol, estado.
 * Quién lo usa: rutas admin.usuarios.* (rol:administrador).
 * Dependencias: ServicioCuentas (cambiarRol, suspender, reactivar, eliminarCuenta).
 * Dueño: Juan (J7).
 */
class UsuarioController extends Controller
{
    public function __construct(private ServicioCuentas $cuentas)
    {
    }

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'rol' => ['nullable', 'in:estandar,familiar,administrador'],
            'estado' => ['nullable', 'in:activos,suspendidos'],
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        $usuarios = User::query()
            ->when($filtros['rol'] ?? null, fn ($q, $rol) => $q->where('rol', $rol))
            ->when(($filtros['estado'] ?? null) === 'activos', fn ($q) => $q->where('activo', true))
            ->when(($filtros['estado'] ?? null) === 'suspendidos', fn ($q) => $q->where('activo', false))
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
        $this->cuentas->cambiarRol($usuario, $request->validated('rol'));

        return back()->with('exito', 'Rol actualizado');
    }

    public function cambiarEstado(ActualizarEstadoRequest $request, User $usuario): RedirectResponse
    {
        $activo = (bool) $request->validated('activo');

        if ($activo) {
            $this->cuentas->reactivar($usuario);
            return back()->with('exito', 'Cuenta activada');
        }

        $this->cuentas->suspender($usuario);
        return back()->with('exito', 'Cuenta suspendida');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        // No puedes eliminar tu propia cuenta desde aquí (evita quedarte fuera).
        if ((int) $request->user()->id === (int) $usuario->id) {
            return back()->withErrors([
                'usuario' => 'No puedes eliminar tu propia cuenta desde aquí.',
            ]);
        }

        $this->cuentas->eliminarCuenta($usuario);

        return back()->with('exito', 'Usuario eliminado');
    }
}