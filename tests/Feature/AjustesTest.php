<?php

namespace Tests\Feature;

use App\Models\Meta;
use App\Models\ParticipacionMeta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas del cambio de tipo de cuenta desde Ajustes (contrato C7).
 * Ejecutar: php artisan test --filter=AjustesTest
 */
class AjustesTest extends TestCase
{
    use RefreshDatabase;

    public function test_estandar_puede_cambiar_a_familiar(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'familiar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));

        $this->assertSame('familiar', $u->fresh()->rol);
    }

    public function test_familiar_sin_metas_puede_bajar_a_estandar(): void
    {
        $u = User::factory()->familiar()->create();
        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));

        $this->assertSame('estandar', $u->fresh()->rol);
    }

    public function test_familiar_con_meta_compartida_activa_no_puede_bajar(): void
    {
        $u = User::factory()->familiar()->create();
        $meta = Meta::factory()->create(['creador_id' => $u->id, 'compartida' => true, 'estado' => 'activa']);
        ParticipacionMeta::create([
            'meta_id' => $meta->id, 'user_id' => $u->id,
            'estado_invitacion' => 'aceptada', 'fecha_union' => now()->toDateString(),
        ]);

        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertSessionHasErrors('tipo_cuenta');
        $this->assertSame('familiar', $u->fresh()->rol);
    }

    public function test_familiar_con_solo_meta_individual_puede_bajar(): void
    {
        $u = User::factory()->familiar()->create();
        $meta = Meta::factory()->create(['creador_id' => $u->id, 'compartida' => false, 'estado' => 'activa']);
        ParticipacionMeta::create([
            'meta_id' => $meta->id, 'user_id' => $u->id,
            'estado_invitacion' => 'aceptada', 'fecha_union' => now()->toDateString(),
        ]);

        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));
        $this->assertSame('estandar', $u->fresh()->rol);
    }

    public function test_tipo_cuenta_lo_puede_ver_cualquier_rol_con_sesion(): void
    {
        $usuarios = [
            User::factory()->create(),                       // estandar (estado por defecto)
            User::factory()->familiar()->create(),
            User::factory()->administrador()->create(),
        ];

        foreach ($usuarios as $u) {
            $this->actingAs($u)->get(route('ajustes.tipo-cuenta'))->assertOk();
        }
    }
}