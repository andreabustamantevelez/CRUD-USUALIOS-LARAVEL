<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Quién puede ver un informe mensual: solo su dueño.
 * Un informe guardado NO se edita ni se borra a mano (en meses cerrados no se recalcula): los
 * genera el servicio GeneradorInforme (V3). update y delete siempre false.
 */
class InformePolicy extends PoliticaDeDueno
{
    public function update(User $usuario, Model $modelo): bool
    {
        return false;
    }

    public function delete(User $usuario, Model $modelo): bool
    {
        return false;
    }
}
