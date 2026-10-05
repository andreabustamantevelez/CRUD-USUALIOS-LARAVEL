<?php

namespace Database\Factories;

use App\Models\GastoFijo;
use App\Models\PagoGastoFijo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PagoGastoFijoFactory extends Factory
{
    protected $model = PagoGastoFijo::class;

    public function definition(): array
    {
        return [
            'gasto_fijo_id'     => GastoFijo::factory(),
            // El pago es del mismo usuario que el gasto fijo:
            'user_id'           => fn (array $a) => GastoFijo::find($a['gasto_fijo_id'])->user_id,
            'periodo'           => '2026-10',
            'fecha_vencimiento' => '2026-10-05',
            'estado'            => 'pendiente',
            'fecha_pago'        => null,
            'monto'             => fn (array $a) => GastoFijo::find($a['gasto_fijo_id'])->monto,
        ];
    }
}
