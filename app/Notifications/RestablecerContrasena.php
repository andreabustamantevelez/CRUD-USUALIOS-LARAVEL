<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * QUÉ HACE : el correo de "restablecer contraseña" en español (Laravel lo trae en inglés).
 * QUIÉN LO USA : User::sendPasswordResetNotification().
 * El enlace usa la ruta password.reset, que ya existe con ese nombre.
 */
class RestablecerContrasena extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $minutos = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Restablece tu contraseña · Cuentas Claras')
            ->greeting('Hola, '.($notifiable->nombre ?? ''))
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line("Este enlace sirve una sola vez y vence en {$minutos} minutos.")
            ->line('Si no fuiste tú, ignora este mensaje: tu contraseña no cambia.')
            ->salutation('Equipo Cuentas Claras');
    }
}
