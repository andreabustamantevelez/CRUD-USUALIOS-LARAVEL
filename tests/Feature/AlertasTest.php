<?php

namespace Tests\Feature;

use App\Models\GastoFijo;
use App\Models\Notificacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertasTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_el_comando_crea_una_alerta_para_un_pago_que_vence_en_3_dias(): void
    {
        Carbon::setTestNow('2026-10-27 08:00:00');

        $usuario = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'nombre' => 'Internet',
            'monto' => 85000,
            'dia_vencimiento' => 30, // 2026-10-30 => +3 días
            'activo' => true,
        ]);

        $this->artisan('alertas:gastos-fijos')->assertSuccessful();

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $usuario->id,
            'tipo' => 'por_vencer',
            'estado' => 'pendiente',
        ]);
    }

    public function test_el_comando_no_duplica_alertas_si_se_corre_dos_veces(): void
    {
        Carbon::setTestNow('2026-10-27 08:00:00');

        $usuario = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'dia_vencimiento' => 30,
            'activo' => true,
        ]);

        $this->artisan('alertas:gastos-fijos')->assertSuccessful();
        $this->artisan('alertas:gastos-fijos')->assertSuccessful();

        $total = Notificacion::where('user_id', $usuario->id)
            ->where('tipo', 'por_vencer')
            ->count();

        $this->assertSame(1, $total);
    }

    public function test_el_comando_crea_alerta_de_vencido_para_pago_vencido(): void
    {
        Carbon::setTestNow('2026-10-27 08:00:00');

        $usuario = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'nombre' => 'Arriendo',
            'dia_vencimiento' => 20, // 2026-10-20 => vencido
            'activo' => true,
        ]);

        $this->artisan('alertas:gastos-fijos')->assertSuccessful();

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $usuario->id,
            'tipo' => 'vencido',
            'estado' => 'pendiente',
        ]);
    }

    public function test_marcar_pagado_cancela_la_notificacion_pendiente(): void
    {
        Carbon::setTestNow('2026-10-27 08:00:00');

        $usuario = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'dia_vencimiento' => 30,
            'activo' => true,
        ]);

        $this->artisan('alertas:gastos-fijos')->assertSuccessful();

        $this->actingAs($usuario)
            ->post(route('gastos-fijos.pagar', $gastoFijo))
            ->assertRedirect(route('gastos-fijos.index'));

        $this->assertDatabaseMissing('notificaciones', [
            'user_id' => $usuario->id,
            'tipo' => 'por_vencer',
            'estado' => 'pendiente',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $usuario->id,
            'tipo' => 'por_vencer',
            'estado' => 'cancelada',
        ]);
    }

    public function test_contar_pendientes_solo_cuenta_las_del_usuario(): void
    {
        $usuarioA = User::factory()->create(['rol' => 'estandar', 'activo' => true]);
        $usuarioB = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        Notificacion::create([
            'user_id' => $usuarioA->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'A',
            'estado' => 'pendiente',
        ]);
        Notificacion::create([
            'user_id' => $usuarioA->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'A2',
            'estado' => 'leida',
        ]);
        Notificacion::create([
            'user_id' => $usuarioB->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'B',
            'estado' => 'pendiente',
        ]);

        $servicio = app(\App\Services\ServicioNotificaciones::class);

        $this->assertSame(1, $servicio->contarPendientes($usuarioA));
        $this->assertSame(1, $servicio->contarPendientes($usuarioB));
    }

    public function test_el_comando_ignora_administradores_y_suspendidos(): void
    {
        Carbon::setTestNow('2026-10-27 08:00:00');

        $admin = User::factory()->administrador()->create(['activo' => true]);
        $suspendido = User::factory()->create(['rol' => 'estandar', 'activo' => false]);

        GastoFijo::factory()->create([
            'user_id' => $admin->id,
            'dia_vencimiento' => 30,
            'activo' => true,
        ]);
        GastoFijo::factory()->create([
            'user_id' => $suspendido->id,
            'dia_vencimiento' => 30,
            'activo' => true,
        ]);

        $this->artisan('alertas:gastos-fijos')->assertSuccessful();

        $this->assertSame(0, Notificacion::count());
    }

    public function test_marcar_todas_leidas_actualiza_solo_las_pendientes_del_usuario(): void
    {
        $usuario = User::factory()->create(['rol' => 'estandar', 'activo' => true]);
        $otro = User::factory()->create(['rol' => 'estandar', 'activo' => true]);

        Notificacion::create([
            'user_id' => $usuario->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'A',
            'estado' => 'pendiente',
        ]);
        Notificacion::create([
            'user_id' => $otro->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'B',
            'estado' => 'pendiente',
        ]);

        $actualizadas = app(\App\Services\ServicioNotificaciones::class)
            ->marcarTodasLeidas($usuario);

        $this->assertSame(1, $actualizadas);
        $this->assertSame(1, Notificacion::where('user_id', $usuario->id)->where('estado', 'leida')->count());
        $this->assertSame(1, Notificacion::where('user_id', $otro->id)->where('estado', 'pendiente')->count());
    }
}