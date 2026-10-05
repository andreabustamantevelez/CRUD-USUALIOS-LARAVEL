<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Cuentas para probar A MANO en el navegador (solo entorno local; contraseña: password).
 * Ya NO hay nombres, cédulas ni montos escritos aquí: los inventa la UserFactory.
 * Lo único fijo son los correos, para poder iniciar sesión.
 * (Las pruebas automáticas NO dependen de este seeder: crean sus propios usuarios con la factory.)
 */
class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $this->crearSiNoExiste('estandar@test.com', fn ($f) => $f);
        $this->crearSiNoExiste('familiar@test.com', fn ($f) => $f->familiar());
        $this->crearSiNoExiste('admin@test.com',    fn ($f) => $f->administrador());
    }

    private function crearSiNoExiste(string $email, callable $estado): void
    {
        if (! User::where('email', $email)->exists()) {
            $estado(User::factory())->create(['email' => $email]);
        }
    }
}
