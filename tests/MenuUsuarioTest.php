<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas del menú del usuario de la barra superior (rediseño del 2026-10-06):
 * Notificaciones, Ajustes y Cerrar sesión viven dentro del avatar.
 * Ejecutar: php artisan test --filter=MenuUsuarioTest
 */
class MenuUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_usuario_estandar_ve_las_tres_opciones_en_el_menu(): void
    {
        $usuario = User::factory()->create(['nombre' => 'Ana Pérez', 'email' => 'ana@correo.com']);

        $this->actingAs($usuario)->get(route('panel'))
            ->assertOk()
            ->assertSee('data-user-menu', false)
            ->assertSee('Ana Pérez')
            ->assertSee('ana@correo.com')
            ->assertSee(route('notificaciones.index'), false)
            ->assertSee(route('ajustes.perfil'), false)
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Cerrar sesión');
    }

    public function test_cerrar_sesion_del_menu_es_un_formulario_post_con_csrf(): void
    {
        $this->actingAs(User::factory()->create())->get(route('panel'))
            ->assertSee('method="POST" action="'.route('logout').'"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_el_administrador_no_ve_notificaciones_ni_nuevo_gasto(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->get(route('admin.estadisticas'))
            ->assertOk()
            ->assertSee('data-user-menu', false)
            ->assertSee(route('ajustes.perfil'), false)
            ->assertDontSee(route('notificaciones.index'), false)
            ->assertDontSee(route('gastos.create'), false)
            ->assertSee('Administrador');
    }

    public function test_el_menu_lateral_ya_no_tiene_ajustes_ni_notificaciones(): void
    {
        $this->actingAs(User::factory()->create())->get(route('panel'))
            ->assertDontSee('>Ajustes</a>', false)
            ->assertDontSee('>Notificaciones</a>', false);

        $this->actingAs(User::factory()->administrador()->create())->get(route('admin.usuarios'))
            ->assertDontSee('>Ajustes</a>', false);
    }

    public function test_las_paginas_cargan_los_estilos_del_menu(): void
    {
        $this->actingAs(User::factory()->create())->get(route('panel'))
            ->assertSee('css/menu-usuario.css', false);
    }
}
