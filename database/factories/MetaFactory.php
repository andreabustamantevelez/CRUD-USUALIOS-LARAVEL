<?php

namespace Database\Factories;

use App\Models\Meta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetaFactory extends Factory
{
    protected $model = Meta::class;

    public function definition(): array
    {
        return [
            'creador_id'     => User::factory()->familiar(),
            'nombre'         => fake()->randomElement(['Viaje a San Andrés', 'Cuota inicial', 'Fondo de emergencia']),
            'monto_objetivo' => 4000000,
            'fecha_limite'   => null,
            'estado'         => 'activa',
        ];
    }
}
