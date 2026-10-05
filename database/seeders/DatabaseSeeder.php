<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([CategoriaSeeder::class]);
        // Los usuarios de prueba solo en entorno local (nunca en producción):
        if (app()->environment('local')) {
            $this->call([UsuariosSeeder::class]);
        }
    }
}
