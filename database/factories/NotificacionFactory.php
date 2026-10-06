<?php

namespace Database\Factories;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificacionFactory extends Factory
{
    protected $model = Notificacion::class;

    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'tipo'               => 'general',
            'mensaje'            => fake()->sentence(),
            'estado'             => 'pendiente',
            'pago_gasto_fijo_id' => null,
            'meta_id'            => null,
            'clave_unica'        => null,
        ];
    }
}
