<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Datos REALES que la aplicación necesita siempre (también en producción):
        $this->call([CategoriaSeeder::class]);

        // Datos de PRUEBA: solo en entorno local, nunca en producción.
        if (app()->environment('local')) {
            $this->call([UsuariosSeeder::class, DatosDePruebaSeeder::class]);
        }
    }
}
