<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * QUÉ HACE : muestra y procesa el registro (rutas registro y registro.guardar).
 * QUIÉN LO USA : routes/modulos/auth.php (middleware guest).
 * DEPENDE DE : RegistroRequest, modelo User (J2).
 * EJEMPLO : POST nombre=Ana Pérez, identificacion=1020304050, correo=ana@correo.com ...
 *           => fila nueva en users con rol=estandar, activo=1, password con hash,
 *              sesión iniciada y redirección al panel con el mensaje "Registro exitoso".
 */
class RegistroController extends Controller
{
    public function create(): View
    {
        return view('publico.crear-usuario');
    }

    public function store(RegistroRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // Los campos del formulario se traducen a las columnas reales de la tabla.
        // 'password' se guarda con hash solo (el modelo User lo declara con cast 'hashed').
        $usuario = new User([
            'nombre'                  => $datos['nombre'],
            'cedula'                  => $datos['identificacion'],
            'email'                   => $datos['correo'],
            'password'                => $datos['password'],
            'consentimiento_datos_at' => now(),
        ]);

        // 'rol' y 'activo' NO son asignables en masa a propósito: se asignan aquí, a mano.
        // El rol sale de tipo_cuenta, que la validación limita a estandar|familiar.
        $usuario->rol    = $datos['tipo_cuenta'];
        $usuario->activo = true;
        $usuario->save();

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('panel')->with('exito', 'Registro exitoso');
    }
}
