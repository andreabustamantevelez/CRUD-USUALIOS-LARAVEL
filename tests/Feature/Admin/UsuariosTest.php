<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_el_listado_de_usuarios(): void
    {
        $admin = User::factory()->administrador()->create();
        User::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.usuarios'))
            ->assertOk();
    }

    public function test_estandar_no_entra_al_listado(): void
    {
        $user = User::factory()->create(['rol' => 'estandar']);
        $this->actingAs($user)->get(route('admin.usuarios'))->assertForbidden();
    }

    public function test_familiar_no_entra_al_listado(): void
    {
        $user = User::factory()->familiar()->create();
        $this->actingAs($user)->get(route('admin.usuarios'))->assertForbidden();
    }

    public function test_sin_sesion_redirige_al_login(): void
    {
        $this->get(route('admin.usuarios'))->assertRedirect(route('login'));
    }

    public function test_admin_puede_cambiar_el_rol_de_un_usuario(): void
    {
        $admin = User::factory()->administrador()->create();
        $objetivo = User::factory()->create(['rol' => 'estandar']);

        $this->actingAs($admin)
            ->put(route('admin.usuarios.rol', $objetivo), ['rol' => 'familiar'])
            ->assertRedirect();

        $this->assertSame('familiar', $objetivo->fresh()->rol);
    }

    public function test_admin_puede_suspender_y_reactivar(): void
    {
        $admin = User::factory()->administrador()->create();
        $objetivo = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        $this->actingAs($admin)
            ->put(route('admin.usuarios.estado', $objetivo), ['activo' => false]);
        $this->assertFalse($objetivo->fresh()->activo);

        $this->actingAs($admin)
            ->put(route('admin.usuarios.estado', $objetivo), ['activo' => true]);
        $this->assertTrue($objetivo->fresh()->activo);
    }

    public function test_no_se_puede_suspender_al_ultimo_admin(): void
    {
        $admin = User::factory()->administrador()->create(['activo' => true]);

        $this->actingAs($admin)
            ->put(route('admin.usuarios.estado', $admin), ['activo' => false])
            ->assertSessionHasErrors();

        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_no_se_puede_cambiar_el_rol_del_ultimo_admin(): void
    {
        $admin = User::factory()->administrador()->create(['activo' => true]);

        $this->actingAs($admin)
            ->put(route('admin.usuarios.rol', $admin), ['rol' => 'estandar'])
            ->assertSessionHasErrors();

        $this->assertSame('administrador', $admin->fresh()->rol);
    }

    public function test_admin_puede_eliminar_a_otro_usuario(): void
    {
        $admin = User::factory()->administrador()->create();
        $objetivo = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.usuarios.destroy', $objetivo))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $objetivo->id]);
    }
}