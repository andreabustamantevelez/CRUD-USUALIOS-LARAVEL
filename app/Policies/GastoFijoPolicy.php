<?php

namespace App\Policies;

/**
 * Quién puede ver, editar y borrar un GastoFijo: solo su dueño (regla en PoliticaDeDueno).
 * Laravel la encuentra sola por el nombre (App\Models\GastoFijo -> App\Policies\GastoFijoPolicy).
 */
class GastoFijoPolicy extends PoliticaDeDueno
{
    // Marcar un gasto fijo como pagado (ruta gastos-fijos.pagar): solo el dueño.
    public function pagar(\App\Models\User $usuario, \Illuminate\Database\Eloquent\Model $modelo): bool
    {
        return $this->esDueno($usuario, $modelo);
    }
}
