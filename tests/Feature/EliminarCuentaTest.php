<?php

namespace Tests\Feature;

use App\Models\AporteMeta;
use App\Models\Gasto;
use App\Models\Meta;
use App\Models\Notificacion;
use App\Models\ParticipacionMeta;
use App\Models\User;
use App\Services\ServicioCuentas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class EliminarCuentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_eliminar_cuenta_borra_todos_los_datos_del_usuario(): void
    {
        $usuario = User::factory()->create();
        Gasto::factory()->count(3)->create(['user_id' => $usuario->id]);

        $this->assertSame(3, Gasto::where('user_id', $usuario->id)->count());

        app(ServicioCuentas::class)->eliminarCuenta($usuario);

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
        $this->assertSame(0, Gasto::where('user_id', $usuario->id)->count());
    }

    public function test_no_se_puede_eliminar_al_ultimo_administrador_activo(): void
    {
        $admin = User::factory()->administrador()->create(['activo' => true]);

        $this->expectException(ValidationException::class);

        app(ServicioCuentas::class)->eliminarCuenta($admin);
    }

    public function test_un_administrador_si_puede_eliminarse_si_hay_otro_activo(): void
    {
        $admin = User::factory()->administrador()->create(['activo' => true]);
        User::factory()->administrador()->create(['activo' => true]);

        app(ServicioCuentas::class)->eliminarCuenta($admin);

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_al_eliminar_un_familiar_con_meta_compartida_activa_se_cancela_y_se_avisa_al_otro(): void
    {
        $creador = User::factory()->familiar()->create();
        $otro = User::factory()->familiar()->create();

        $meta = Meta::factory()->create([
            'creador_id' => $creador->id,
            'compartida' => true,
            'estado' => 'activa',
        ]);

        ParticipacionMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $creador->id,
            'estado_invitacion' => 'aceptada',
        ]);
        ParticipacionMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $otro->id,
            'estado_invitacion' => 'aceptada',
        ]);

        AporteMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $creador->id,
            'monto' => 500000,
            'fecha' => now()->toDateString(),
        ]);

        app(ServicioCuentas::class)->eliminarCuenta($creador);

        $this->assertSame('cancelada', $meta->fresh()->estado);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $otro->id,
            'tipo' => 'meta_abandonada',
            'meta_id' => $meta->id,
        ]);

        // El otro usuario sigue existiendo.
        $this->assertDatabaseHas('users', ['id' => $otro->id]);
    }

    public function test_eliminar_a_un_usuario_no_toca_los_datos_de_otros(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        Gasto::factory()->count(2)->create(['user_id' => $usuarioA->id]);
        Gasto::factory()->count(5)->create(['user_id' => $usuarioB->id]);

        app(ServicioCuentas::class)->eliminarCuenta($usuarioA);

        $this->assertSame(5, Gasto::where('user_id', $usuarioB->id)->count());
        $this->assertSame(0, Notificacion::where('user_id', $usuarioA->id)->count());
    }

    public function test_una_meta_individual_no_genera_notificacion_al_eliminar_cuenta(): void
    {
        $usuario = User::factory()->create();

        Meta::factory()->create([
            'creador_id' => $usuario->id,
            'compartida' => false,
            'estado' => 'activa',
        ]);

        app(ServicioCuentas::class)->eliminarCuenta($usuario);

        $this->assertSame(0, Notificacion::where('tipo', 'meta_abandonada')->count());
    }
}