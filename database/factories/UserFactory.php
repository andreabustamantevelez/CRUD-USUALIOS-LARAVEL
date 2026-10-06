<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * REEMPLAZA a la UserFactory que trae Laravel (usaba 'name', que ya no existe).
 * Genera usuarios con datos inventados. Uso:
 *   User::factory()->create();                 // estándar
 *   User::factory()->familiar()->create();
 *   User::factory()->administrador()->create();
 *   User::factory()->count(5)->create();
 *   User::factory()->create(['email' => 'x@x.com']);   // sobreescribir un campo
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nombre'          => fake()->name(),
            'cedula'          => fake()->unique()->numerify('##########'),
            'email'           => fake()->unique()->safeEmail(),
            'password'        => 'password',     // el cast 'hashed' del modelo la convierte en hash
            'rol'             => 'estandar',
            'activo'          => true,
            'ingreso_mensual' => fake()->numberBetween(15, 80) * 100000,   // 1.500.000 a 8.000.000 COP
            'consentimiento_datos_at' => now(),
            'remember_token'  => Str::random(10),
        ];
    }

    public function familiar(): static       { return $this->state(fn () => ['rol' => 'familiar']); }
    public function administrador(): static  { return $this->state(fn () => ['rol' => 'administrador', 'ingreso_mensual' => null]); }
    public function inactivo(): static       { return $this->state(fn () => ['activo' => false]); }
    public function sinIngreso(): static     { return $this->state(fn () => ['ingreso_mensual' => null]); }
}
