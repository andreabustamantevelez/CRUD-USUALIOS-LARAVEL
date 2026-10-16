<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriasSistemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_el_listado(): void
    {
        $admin = User::factory()->administrador()->create();
        $this->actingAs($admin)->get(route('admin.categorias'))->assertOk();
    }

    public function test_no_admin_recibe_403(): void
    {
        $user = User::factory()->create(['rol' => 'estandar']);
        $this->actingAs($user)->get(route('admin.categorias'))->assertForbidden();
    }

    public function test_admin_crea_una_categoria_del_sistema(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->post(route('admin.categorias.store'), [
            'nombre' => 'Nueva del sistema',
            'bloque' => 'necesidades',
            'peso_recomendado' => 50,
        ])->assertRedirect();

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Nueva del sistema',
            'user_id' => null,
            'activa' => true,
        ]);
    }

    public function test_admin_no_puede_duplicar_nombre(): void
    {
        $admin = User::factory()->administrador()->create();
        Categoria::factory()->delSistema()->create(['nombre' => 'Mercado']);

        $this->actingAs($admin)
            ->post(route('admin.categorias.store'), [
                'nombre' => 'Mercado',
                'bloque' => 'necesidades',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_desactivar_cambia_el_estado(): void
    {
        $admin = User::factory()->administrador()->create();
        $categoria = Categoria::factory()->delSistema()->create(['activa' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.categorias.desactivar', $categoria))
            ->assertRedirect();

        $this->assertFalse($categoria->fresh()->activa);
    }

    public function test_no_se_puede_desactivar_una_categoria_personal(): void
    {
        $admin = User::factory()->administrador()->create();
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->patch(route('admin.categorias.desactivar', $categoria))
            ->assertForbidden();
    }
}