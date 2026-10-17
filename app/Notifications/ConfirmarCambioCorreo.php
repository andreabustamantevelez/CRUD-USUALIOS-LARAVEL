<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Envía al NUEVO correo (users.pending_email) un enlace para confirmar el
 * cambio de correo. El correo actual sigue activo hasta que se confirme.
 *
 * IMPORTANTE: se envía con
 *   Notification::route('mail', $usuario->pending_email)->notify(...)
 * desde ServicioCuentas. NO uses $usuario->notify(...) directo, porque caería
 * al correo viejo y la verificación no serviría.
 */
class ConfirmarCambioCorreo extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
        public User $usuario,
        public string $expiraEn = '1 hora',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('ajustes.confirmar-correo', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('Confirma tu nuevo correo — Cuentas Claras')
            ->view('emails.confirmar-cambio-correo', [
                'url'         => $url,
                'usuario'     => $this->usuario,
                'nuevoCorreo' => $this->usuario->pending_email,
                'expiraEn'    => $this->expiraEn,
            ]);
    }
}