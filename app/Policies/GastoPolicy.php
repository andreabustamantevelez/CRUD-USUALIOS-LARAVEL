<?php

namespace App\Policies;

/**
 * Quién puede ver, editar y borrar un Gasto: solo su dueño (regla en PoliticaDeDueno).
 * Laravel la encuentra sola por el nombre (App\Models\Gasto -> App\Policies\GastoPolicy).
 */
class GastoPolicy extends PoliticaDeDueno
{
}
