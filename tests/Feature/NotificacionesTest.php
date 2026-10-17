<?php

namespace Tests\Feature;

use App\Models\Notificacion;
use App\Models\User;
use App\Services\ServicioNotificaciones;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificacionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_ver_sus_notificaciones(): void
    {
        $usuario = User::factory()->create();

        Notificacion::factory()->create([
            'user_id' => $usuario->id,
            'mensaje' => 'Tu gasto de internet está próximo a vencer.',
        ]);

        $respuesta = $this->actingAs($usuario)
            ->get(route('notificaciones.index'));

        $respuesta->assertOk();
        $respuesta->assertSee('Tu gasto de internet está próximo a vencer.');
    }

    public function test_usuario_puede_marcar_una_notificacion_como_leida(): void
    {
        $usuario = User::factory()->create();

        $notificacion = Notificacion::factory()->create([
            'user_id' => $usuario->id,
            'estado' => ServicioNotificaciones::ESTADO_PENDIENTE,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->post(route('notificaciones.leer'), [
                'notificacion_id' => $notificacion->id,
            ]);

        $respuesta->assertRedirect(route('notificaciones.index'));

        $this->assertDatabaseHas('notificaciones', [
            'id' => $notificacion->id,
            'user_id' => $usuario->id,
            'estado' => ServicioNotificaciones::ESTADO_LEIDA,
        ]);
    }

    public function test_usuario_puede_marcar_todas_sus_notificaciones_como_leidas(): void
    {
        $usuario = User::factory()->create();

        Notificacion::factory()->count(3)->create([
            'user_id' => $usuario->id,
            'estado' => ServicioNotificaciones::ESTADO_PENDIENTE,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->post(route('notificaciones.leerTodas'));

        $respuesta->assertRedirect(route('notificaciones.index'));

        $this->assertDatabaseMissing('notificaciones', [
            'user_id' => $usuario->id,
            'estado' => ServicioNotificaciones::ESTADO_PENDIENTE,
        ]);

        $this->assertDatabaseCount('notificaciones', 3);
    }

    public function test_usuario_no_puede_marcar_como_leida_una_notificacion_ajena(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $notificacion = Notificacion::factory()->create([
            'user_id' => $otroUsuario->id,
            'estado' => ServicioNotificaciones::ESTADO_PENDIENTE,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->post(route('notificaciones.leer'), [
                'notificacion_id' => $notificacion->id,
            ]);

        $respuesta->assertForbidden();

        $this->assertDatabaseHas('notificaciones', [
            'id' => $notificacion->id,
            'user_id' => $otroUsuario->id,
            'estado' => ServicioNotificaciones::ESTADO_PENDIENTE,
        ]);
    }

    public function test_usuario_sin_notificaciones_ve_estado_vacio(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)
            ->get(route('notificaciones.index'));

        $respuesta->assertOk();
        $respuesta->assertSee('No tienes notificaciones pendientes.');
        $respuesta->assertSee('Sin notificaciones anteriores.');
    }

    public function test_lista_de_notificaciones_tiene_paginacion(): void
    {
        $usuario = User::factory()->create();

        Notificacion::factory()->count(12)->create([
            'user_id' => $usuario->id,
            'estado' => ServicioNotificaciones::ESTADO_PENDIENTE,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->get(route('notificaciones.index'));

        $respuesta->assertOk();

        $respuesta->assertViewHas('pendientes', function ($pendientes) {
            return $pendientes->total() === 12
                && $pendientes->count() === 10;
        });
    }
}
