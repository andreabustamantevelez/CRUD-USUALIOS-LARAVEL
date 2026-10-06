<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Quién puede ver y borrar una simulación de crédito: solo su dueño.
 * Una simulación guardada NO se edita (se calcula otra y se guarda como nueva): update siempre false.
 */
class SimulacionCreditoPolicy extends PoliticaDeDueno
{
    public function update(User $usuario, Model $modelo): bool
    {
        return false;
    }
}
