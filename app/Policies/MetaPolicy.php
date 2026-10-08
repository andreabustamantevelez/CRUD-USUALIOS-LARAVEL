<?php

namespace App\Policies;

use App\Models\Meta;
use App\Models\User;

/**
 * Quién puede hacer qué con una Meta (solo PARTICIPANTES aceptados).
 *   - Administrador: nunca (before). Estándar: nunca (solo familiar).
 *   - ver / aportar / finalizar / abandonar: participante aceptado (aportar, finalizar y abandonar
 *     además exigen meta activa).
 *   - invitar: solo quien CREÓ la meta, con la meta COMPARTIDA y activa (una meta individual no admite invitados).
 * Privacidad de aportes: la Policy de AporteMeta (solo el dueño ve su aporte) y las consultas por
 * usuario hacen el resto; ver la meta NO da acceso a los aportes del otro.
 */
class MetaPolicy
{
    public function before(User $usuario, string $habilidad): ?bool
    {
        return $usuario->rol === 'administrador' ? false : null;   // estandar y familiar pasan; admin nunca
    }

    public function viewAny(User $usuario): bool
    {
        return true;
    }

    public function create(User $usuario): bool
    {
        return true;
    }

    public function view(User $usuario, Meta $meta): bool
    {
        return $meta->esParticipante($usuario);
    }

    public function invitar(User $usuario, Meta $meta): bool
    {
        return $meta->esCompartida() && $meta->estaActiva()
            && $usuario->id === (int) $meta->creador_id && $meta->esParticipante($usuario);
    }

    public function aportar(User $usuario, Meta $meta): bool
    {
        return $meta->estaActiva() && $meta->esParticipante($usuario);
    }

    public function finalizar(User $usuario, Meta $meta): bool
    {
        return $meta->estaActiva() && $meta->esParticipante($usuario);
    }

    public function abandonar(User $usuario, Meta $meta): bool
    {
        return $meta->estaActiva() && $meta->esParticipante($usuario);
    }

    public function update(User $usuario, Meta $meta): bool
    {
        return false;   // una meta no se edita después de creada
    }

    public function delete(User $usuario, Meta $meta): bool
    {
        return false;   // se cancela (abandonar), no se borra: conserva el historial
    }
}
