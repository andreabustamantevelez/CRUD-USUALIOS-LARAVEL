<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * QUÉ HACE : regla base de las Policies de datos personales: solo el DUEÑO (user_id) ve y cambia lo suyo.
 * QUIÉN LA USA : GastoPolicy, GastoFijoPolicy, SimulacionCreditoPolicy, NotificacionPolicy, InformePolicy.
 * REGLAS:
 *   - El administrador NUNCA toca datos financieros (before devuelve false): defensa extra además
 *     del middleware 'rol'.
 *   - viewAny y create dependen solo de tener sesión (el listado se filtra con el scope delUsuario).
 * USO EN UN CONTROLADOR:
 *   $this->authorize('update', $gasto);            // 403 si el gasto es de otra persona
 *   $this->authorize('viewAny', Gasto::class);     // antes de listar
 */
abstract class PoliticaDeDueno
{
    public function before(User $usuario, string $habilidad): ?bool
    {
        return $usuario->rol === 'administrador' ? false : null;
    }

    public function viewAny(User $usuario): bool
    {
        return true;
    }

    public function create(User $usuario): bool
    {
        return true;
    }

    public function view(User $usuario, Model $modelo): bool
    {
        return $this->esDueno($usuario, $modelo);
    }

    public function update(User $usuario, Model $modelo): bool
    {
        return $this->esDueno($usuario, $modelo);
    }

    public function delete(User $usuario, Model $modelo): bool
    {
        return $this->esDueno($usuario, $modelo);
    }

    protected function esDueno(User $usuario, Model $modelo): bool
    {
        return $modelo->user_id !== null && $usuario->id === (int) $modelo->user_id;
    }
}
