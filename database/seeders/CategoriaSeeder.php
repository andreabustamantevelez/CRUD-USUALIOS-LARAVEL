<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

/** Categorías DEL SISTEMA (user_id = null). Equivalencia propuesta (decisión #12). */
class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $lista = [
            'necesidades'    => ['Arriendo', 'Servicios públicos', 'Mercado', 'Transporte', 'Salud', 'Educación'],
            'discrecionales' => ['Restaurantes', 'Entretenimiento', 'Ropa', 'Suscripciones', 'Viajes'],
            'ahorro'         => ['Ahorro', 'Inversión', 'Pago extra de deudas'],
        ];
        $peso = ['necesidades' => 50, 'discrecionales' => 30, 'ahorro' => 20];

        foreach ($lista as $bloque => $nombres) {
            foreach ($nombres as $nombre) {
                // firstOrCreate: se puede correr el seeder varias veces sin duplicar.
                Categoria::firstOrCreate(
                    ['user_id' => null, 'nombre' => $nombre],
                    ['bloque' => $bloque, 'peso_recomendado' => $peso[$bloque]]
                );
            }
        }
    }
}
