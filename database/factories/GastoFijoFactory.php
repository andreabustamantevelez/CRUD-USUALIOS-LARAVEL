<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\GastoFijo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GastoFijoFactory extends Factory
{
    protected $model = GastoFijo::class;

    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'categoria_id'    => Categoria::factory()->delSistema(),
            'nombre'          => fake()->randomElement(['Arriendo', 'Internet', 'Celular', 'Gimnasio', 'Streaming']),
            'monto'           => fake()->numberBetween(30, 1500) * 1000,
            'dia_vencimiento' => fake()->numberBetween(1, 28),
            'periodicidad'    => 'mensual',
            'descripcion'     => null,
            'activo'          => true,
        ];
    }
}
