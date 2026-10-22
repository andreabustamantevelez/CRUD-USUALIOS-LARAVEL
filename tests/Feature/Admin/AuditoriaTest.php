<?php

namespace Tests\Feature\Admin;

use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_la_vista_de_auditoria(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)
            ->get(route('admin.auditoria'))
            ->assertOk()
            ->assertSee('Auditoría');
    }

    public function test_estandar_recibe_403(): void
    {
        $estandar = User::factory()->create();

        $this->actingAs($estandar)
            ->get(route('admin.auditoria'))
            ->assertForbidden();
    }

    public function test_sin_sesion_redirige_al_login(): void
    {
        $this->get(route('admin.auditoria'))->assertRedirect(route('login'));
    }

    public function test_cambiar_rol_genera_registro_de_auditoria(): void
    {
        $admin = User::factory()->administrador()->create();
        $objetivo = User::factory()->create(['rol' => 'estandar']);

        $this->actingAs($admin)
            ->put(route('admin.usuarios.rol', $objetivo), ['rol' => 'familiar']);

        $this->assertDatabaseHas('auditorias', [
            'user_id'   => $admin->id,
            'accion'    => 'usuario.rol',
            'modelo'    => 'User',
            'modelo_id' => $objetivo->id,
        ]);

        $auditoria = Auditoria::latest('id')->first();
        $this->assertSame('estandar', $auditoria->antes['rol']);
        $this->assertSame('familiar', $auditoria->despues['rol']);
    }

    public function test_eliminar_usuario_genera_registro_de_auditoria(): void
    {
        $admin = User::factory()->administrador()->create(['password' => bcrypt('secreto123')]);
        $objetivo = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.usuarios.destroy', $objetivo), [
                'password' => 'secreto123',
            ]);

        $this->assertDatabaseHas('auditorias', [
            'user_id'   => $admin->id,
            'accion'    => 'usuario.eliminar',
            'modelo_id' => $objetivo->id,
        ]);
    }

    public function test_desactivar_categoria_genera_registro_de_auditoria(): void
    {
        $admin = User::factory()->administrador()->create(['password' => bcrypt('secreto123')]);
        $categoria = Categoria::factory()->delSistema()->create(['activa' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.categorias.desactivar', $categoria), [
                'password' => 'secreto123',
            ]);

        $this->assertDatabaseHas('auditorias', [
            'user_id'   => $admin->id,
            'accion'    => 'categoria.desactivar',
            'modelo_id' => $categoria->id,
        ]);
    }
}