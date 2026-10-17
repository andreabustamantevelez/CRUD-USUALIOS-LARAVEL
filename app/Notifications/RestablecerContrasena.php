<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Correo de "restablecer contraseña" en español.
 * Usa la vista emails.restablecer-contrasena con el mismo diseño que
 * emails.confirmar-cambio-correo (misma estética, distinto contenido).
 * El enlace usa la ruta password.reset, con vencimiento leído de
 * config/auth.php (passwords.users.expire), hoy 30 minutos.
 */
class RestablecerContrasena extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        $minutos = (int) config(
            'auth.passwords.' . config('auth.defaults.passwords') . '.expire',
            30
        );

        return (new MailMessage)
            ->subject('Restablece tu contraseña — Cuentas Claras')
            ->view('emails.restablecer-contrasena', [
                'usuario'  => $notifiable,
                'correo'   => $notifiable->email,
                'url'      => $url,
                'expiraEn' => $minutos . ' minutos',
            ]);
    }
}