<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GastoFactory extends Factory
{
    protected $model = Gasto::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'categoria_id' => Categoria::factory()->delSistema(),
            'monto'        => fake()->numberBetween(10, 5000) * 100,      // pesos enteros: 1.000 a 500.000
            'fecha'        => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'descripcion'  => fake()->sentence(3),
        ];
    }
}
