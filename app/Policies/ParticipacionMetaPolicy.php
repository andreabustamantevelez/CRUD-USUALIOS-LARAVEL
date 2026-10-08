<?php

namespace App\Policies;

use App\Models\ParticipacionMeta;
use App\Models\User;

/**
 * Aceptar o rechazar una invitación: SOLO la persona invitada, mientras esté pendiente.
 * (Ni la creadora de la meta ni un tercero pueden responder por ella.)
 *
 * NOTA (2026-10-08): el estándar también pasa esta Policy. Si intenta aceptar una meta
 * compartida, ServicioMetas::aceptar() le devuelve un mensaje amable ("cambia tu tipo de
 * cuenta a familiar en Ajustes") en lugar de un 403 seco. El administrador sigue bloqueado.
 */
class ParticipacionMetaPolicy
{
    public function before(User $usuario, string $habilidad): ?bool
    {
        return $usuario->rol === 'administrador' ? false : null;
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