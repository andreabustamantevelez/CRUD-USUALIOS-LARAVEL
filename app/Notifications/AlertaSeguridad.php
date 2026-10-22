<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Correo de alerta de seguridad.
 *
 * Eventos: password.changed, email.changed, account.suspended,
 *          login.new_ip, login.blocked.
 *
 * Implementa ShouldQueue: no bloquea el flujo (login, confirmar correo, etc.)
 * si el SMTP falla. El worker reintenta.
 */
class AlertaSeguridad extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $evento,
        public array $detalles = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $asuntos = [
            'password.changed'  => 'Tu contraseña fue cambiada — Cuentas Claras',
            'email.changed'     => 'Tu correo de Cuentas Claras cambió',
            'account.suspended' => 'Tu cuenta fue suspendida — Cuentas Claras',
            'login.new_ip'      => 'Nuevo inicio de sesión en Cuentas Claras',
            'login.blocked'     => 'Intentos de inicio de sesión bloqueados — Cuentas Claras',
        ];

        // El notifiable puede ser un User o un AnonymousNotifiable (por mail).
        $nombre = $notifiable instanceof \App\Models\User
            ? $notifiable->nombre
            : ($this->detalles['nombre'] ?? null);

        return (new MailMessage)
            ->subject($asuntos[$this->evento] ?? 'Alerta de seguridad — Cuentas Claras')
            ->view('emails.alerta-seguridad', [
                'evento'   => $this->evento,
                'detalles' => $this->detalles,
                'nombre'   => $nombre,
            ]);
    }
}