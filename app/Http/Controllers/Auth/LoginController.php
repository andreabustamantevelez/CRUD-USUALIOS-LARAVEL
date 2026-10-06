<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * QUÉ HACE : iniciar y cerrar sesión (rutas login, login.entrar y logout).
 * QUIÉN LO USA : routes/modulos/auth.php.
 * DEPENDE DE : LoginRequest (validación + límite de intentos).
 * Redirección: administrador -> admin.estadisticas; estandar/familiar -> panel. Si la persona
 * intentó entrar antes a una URL protegida, vuelve a esa URL (intended).
 */
class LoginController extends Controller
{
    public function create(): View
    {
        return view('publico.iniciar-sesion');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar();
        $request->session()->regenerate();

        $destino = Auth::user()->rol === 'administrador'
            ? route('admin.estadisticas')
            : route('panel');

        return redirect()->intended($destino);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
