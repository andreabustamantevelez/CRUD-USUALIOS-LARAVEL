<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\GastoFijo;
use App\Models\Meta;
use App\Models\Notificacion;
use App\Models\ParticipacionMeta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_nuevo_ve_la_bienvenida(): void
    {
        $usuario = User::factory()->sinIngreso()->create(['rol' => 'estandar']);

        $this->actingAs($usuario)
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('Bienvenido a Cuentas Claras')
            ->assertSee('Registrar tu ingreso');
    }

    public function test_usuario_con_datos_ve_sus_metricas(): void
    {
        $usuario = User::factory()->create([
            'rol' => 'estandar',
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create(['bloque' => 'necesidades']);

        Gasto::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 500000,
            'fecha' => now()->toDateString(),
        ]);

        $this->actingAs($usuario)
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('3.000.000')
            ->assertSee('500.000')
            ->assertSee('2.500.000');
    }

    public function test_solo_ve_sus_propios_gastos(): void
    {
        $usuarioA = User::factory()->create(['rol' => 'estandar', 'ingreso_mensual' => 1000000]);
        $usuarioB = User::factory()->create(['rol' => 'estandar']);

        $categoria = Categoria::factory()->delSistema()->create();

        Gasto::factory()->create([
            'user_id' => $usuarioA->id,
            'categoria_id' => $categoria->id,
            'monto' => 111111,
            'fecha' => now()->toDateString(),
        ]);

        Gasto::factory()->create([
            'user_id' => $usuarioB->id,
            'categoria_id' => $categoria->id,
            'monto' => 999999,
            'fecha' => now()->toDateString(),
        ]);

        $this->actingAs($usuarioA)
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('111.111')
            ->assertDontSee('999.999');
    }

    public function test_familiar_ve_tarjeta_de_metas(): void
    {
        $familiar = User::factory()->familiar()->create(['ingreso_mensual' => 2000000]);

        $meta = Meta::factory()->create([
            'creador_id' => $familiar->id,
            'nombre' => 'Viaje familiar',
            'compartida' => false,
            'estado' => 'activa',
        ]);

        ParticipacionMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $familiar->id,
            'estado_invitacion' => 'aceptada',
        ]);

        $this->actingAs($familiar)
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('Mis metas')
            ->assertSee('Viaje familiar');
    }

    public function test_estandar_no_ve_tarjeta_de_metas(): void
    {
        $estandar = User::factory()->create([
            'rol' => 'estandar',
            'ingreso_mensual' => 2000000,
        ]);

        $this->actingAs($estandar)
            ->get(route('panel'))
            ->assertOk()
            ->assertDontSee('Mis metas');
    }

    public function test_administrador_recibe_403_en_panel(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)
            ->get(route('panel'))
            ->assertForbidden();
    }

    public function test_sin_sesion_redirige_a_iniciar_sesion(): void
    {
        $this->get(route('panel'))
            ->assertRedirect(route('login'));
    }

    public function test_no_muestra_notificaciones_de_otros(): void
    {
        $usuarioA = User::factory()->create(['rol' => 'estandar']);
        $usuarioB = User::factory()->create(['rol' => 'estandar']);

        Notificacion::create([
            'user_id' => $usuarioA->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'Tu internet vence pronto',
            'estado' => 'pendiente',
        ]);

        Notificacion::create([
            'user_id' => $usuarioB->id,
            'tipo' => 'por_vencer',
            'mensaje' => 'Tu arriendo vence pronto',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($usuarioA)
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('Tu internet vence pronto')
            ->assertDontSee('Tu arriendo vence pronto');
    }

    public function test_proximos_vencimientos_muestra_los_gastos_fijos_cercanos(): void
    {
        $usuario = User::factory()->create([
            'rol' => 'estandar',
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create();

        $diaEn3Dias = now()->copy()->addDays(3)->day;

        GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Internet del mes',
            'monto' => 85000,
            'dia_vencimiento' => $diaEn3Dias,
            'activo' => true,
        ]);

        $this->actingAs($usuario)
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('Internet del mes');
    }
}