<?php

namespace Database\Factories;

use App\Models\SimulacionCredito;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Valores del caso calculado de V1: $10.000.000 al 1,5 % mensual a 24 meses. */
class SimulacionCreditoFactory extends Factory
{
    protected $model = SimulacionCredito::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'nombre'       => 'Carro nuevo',
            'monto'        => 10000000,
            'tasa_mensual' => 1.5,
            'plazo_meses'  => 24,
            'cuota'        => 499241,
            'total_pagar'  => 11981784,
            'intereses'    => 1981784,
        ];
    }
}
