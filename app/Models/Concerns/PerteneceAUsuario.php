<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * QUÉ HACE : agrega el filtro "solo lo de este usuario" a un modelo que tiene la columna user_id.
 * QUIÉN LO USA : Gasto, GastoFijo, PagoGastoFijo, SimulacionCredito, Informe, Notificacion.
 * REGLA : en listados y totales SIEMPRE se parte de aquí; nunca Gasto::all() ni Gasto::find($id) sin
 *         filtrar por usuario (y para un registro puntual, además $this->authorize()).
 * EJEMPLO : Gasto::delUsuario(auth()->id())->whereMonth('fecha', 9)->sum('monto');
 */
trait PerteneceAUsuario
{
    public function scopeDelUsuario(Builder $consulta, int $usuarioId): Builder
    {
        return $consulta->where($this->getTable().'.user_id', $usuarioId);
    }
}
