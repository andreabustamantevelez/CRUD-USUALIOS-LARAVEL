<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CuentaActiva;
use App\Http\Middleware\RolRequerido;
use App\Http\Middleware\ExpirarSesionPorInactividad;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Un usuario ya autenticado que visita /iniciar-sesion o /crear-usuario
        // es redirigido aquí. El admin va a /admin/panel; estandar|familiar a /panel.
        $middleware->redirectUsersTo(
            fn () => auth()->user()?->rol === 'administrador'
                ? route('admin.panel')
                : route('panel')
        );

        $middleware->alias([
            'cuenta.activa'      => CuentaActiva::class,
            'rol'                => RolRequerido::class,
            'sesion.inactividad' => ExpirarSesionPorInactividad::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();