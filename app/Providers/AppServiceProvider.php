<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Qué hace: registra el View Composer que comparte $notificacionesPendientes
 *           (contrato C8) con el armazón privado (layouts y barra superior).
 * Quién lo usa: components/topbar.blade.php (campana).
 * Dueño: Juan (J1). En J6 se cambia el 0 por ServicioNotificaciones::contarPendientes(auth()->user()).
 * Ejemplo: usuario con 3 alertas sin leer -> la campana muestra 3.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.admin', 'components.topbar'], function ($view) {
            $view->with('notificacionesPendientes', 0); // J6: reemplazar por el conteo real
        });
    }
}
