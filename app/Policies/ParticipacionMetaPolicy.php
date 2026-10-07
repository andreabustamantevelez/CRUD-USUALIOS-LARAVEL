<?php

namespace App\Policies;

use App\Models\ParticipacionMeta;
use App\Models\User;

/**
 * Aceptar o rechazar una invitación: SOLO la persona invitada, mientras esté pendiente.
 * (Ni la creadora de la meta ni un tercero pueden responder por ella.)
 */
class ParticipacionMetaPolicy
{
    public function before(User $usuario, string $habilidad): ?bool
    {
        return $usuario->rol === 'familiar' ? null : false;
    }

    public function aceptar(User $usuario, ParticipacionMeta $participacion): bool
    {
        return $this->esInvitadaPendiente($usuario, $participacion);
    }

    public function rechazar(User $usuario, ParticipacionMeta $participacion): bool
    {
        return $this->esInvitadaPendiente($usuario, $participacion);
    }

    private function esInvitadaPendiente(User $usuario, ParticipacionMeta $p): bool
    {
        return $usuario->id === (int) $p->user_id && $p->estado_invitacion === 'pendiente';
    }
}
