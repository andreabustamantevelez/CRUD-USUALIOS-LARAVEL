<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),        // por defecto: categoría PROPIA
            'nombre'           => fake()->unique()->words(2, true),
            'bloque'           => fake()->randomElement(Categoria::BLOQUES),
            'peso_recomendado' => null,
        ];
    }

    /** Categoría del sistema (sin dueño). */
    public function delSistema(): static { return $this->state(fn () => ['user_id' => null]); }
}
