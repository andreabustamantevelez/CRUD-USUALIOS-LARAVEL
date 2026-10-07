<?php

namespace App\Policies;

use App\Models\AporteMeta;
use App\Models\User;

/**
 * Un aporte lo ve SOLO quien lo hizo (RQNF-03). Nadie lo edita ni lo borra: es historial.
 */
class AporteMetaPolicy extends PoliticaDeDueno
{
    public function update(User $usuario, \Illuminate\Database\Eloquent\Model $modelo): bool
    {
        return false;
    }

    public function delete(User $usuario, \Illuminate\Database\Eloquent\Model $modelo): bool
    {
        return false;
    }
}
