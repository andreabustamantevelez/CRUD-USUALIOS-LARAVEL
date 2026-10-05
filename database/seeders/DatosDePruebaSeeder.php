<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Gastos inventados para que las pantallas no se vean vacías mientras se desarrolla (solo local). */
class DatosDePruebaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = Categoria::whereNull('user_id')->pluck('id');
        if ($categorias->isEmpty()) {
            return;   // primero debe correr CategoriaSeeder
        }
        foreach (User::whereIn('rol', ['estandar', 'familiar'])->get() as $usuario) {
            if ($usuario->gastos()->exists()) {
                continue;   // no duplicar si se vuelve a ejecutar
            }
            for ($i = 0; $i < 20; $i++) {
                Gasto::factory()->create(['user_id' => $usuario->id, 'categoria_id' => $categorias->random()]);
            }
        }
    }
}
