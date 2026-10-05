<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Usuarios de PRUEBA (solo desarrollo local). Contraseña de todos: password */
class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            ['Ana Estándar',   '1000000001', 'estandar@test.com',  'estandar',      3000000],
            ['Luis Familiar',  '1000000002', 'familiar@test.com',  'familiar',      2500000],
            ['Admin',          '1000000003', 'admin@test.com',     'administrador', null],
        ];
        foreach ($datos as [$nombre, $cedula, $email, $rol, $ingreso]) {
            $u = User::firstOrNew(['email' => $email]);
            $u->fill(['nombre' => $nombre, 'cedula' => $cedula, 'password' => 'password', 'ingreso_mensual' => $ingreso]);
            $u->rol = $rol;        // 'rol' no es "fillable": se asigna a mano a propósito
            $u->activo = true;
            $u->save();
        }
    }
}
