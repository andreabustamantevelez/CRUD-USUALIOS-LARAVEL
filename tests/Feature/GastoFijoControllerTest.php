<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\GastoFijo;
use App\Models\PagoGastoFijo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GastoFijoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_ver_la_pantalla_de_gastos_fijos(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)
            ->get(route('gastos-fijos.index'));

        $respuesta->assertOk();
    }

    public function test_usuario_puede_crear_un_gasto_fijo(): void
    {
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->delSistema()->create();

        $respuesta = $this->actingAs($usuario)
            ->post(route('gastos-fijos.store'), [
                'nombre' => 'Pago de internet',
                'monto' => 85000,
                'dia_vencimiento' => 15,
                'categoria_id' => $categoria->id,
                'periodicidad' => 'mensual',
                'descripcion' => 'Internet del hogar',
                'activo' => 1,
            ]);

        $respuesta->assertRedirect(route('gastos-fijos.index'));

        $this->assertDatabaseHas('gastos_fijos', [
            'user_id' => $usuario->id,
            'nombre' => 'Pago de internet',
            'monto' => 85000,
            'dia_vencimiento' => 15,
        ]);
    }

    public function test_usuario_no_puede_crear_un_gasto_fijo_con_monto_invalido(): void
    {
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->delSistema()->create();

        $respuesta = $this->actingAs($usuario)
            ->from(route('gastos-fijos.index'))
            ->post(route('gastos-fijos.store'), [
                'nombre' => 'Pago de internet',
                'monto' => 0,
                'dia_vencimiento' => 15,
                'categoria_id' => $categoria->id,
                'periodicidad' => 'mensual',
            ]);

        $respuesta->assertSessionHasErrors('monto');

        $this->assertDatabaseMissing('gastos_fijos', [
            'user_id' => $usuario->id,
            'nombre' => 'Pago de internet',
        ]);
    }

    public function test_usuario_puede_marcar_un_gasto_fijo_como_pagado(): void
    {
        $usuario = User::factory()->create();

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->post(route('gastos-fijos.pagar', $gastoFijo));

        $respuesta->assertRedirect(route('gastos-fijos.index'));

        $this->assertDatabaseHas('pagos_gasto_fijo', [
            'user_id' => $usuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'estado' => 'pagado',
        ]);
    }

    public function test_usuario_no_puede_pagar_el_gasto_fijo_de_otro_usuario(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $otroUsuario->id,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->post(route('gastos-fijos.pagar', $gastoFijo));

        $respuesta->assertForbidden();

        $this->assertDatabaseMissing('pagos_gasto_fijo', [
            'user_id' => $usuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'estado' => 'pagado',
        ]);
    }
}
