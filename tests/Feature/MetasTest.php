<?php

namespace Tests\Feature;

use App\Models\AporteMeta;
use App\Models\Meta;
use App\Models\Notificacion;
use App\Models\ParticipacionMeta;
use App\Models\User;
use App\Services\ServicioMetas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de J5: metas compartidas (crear, invitar, aceptar/rechazar, aportes, avance, privacidad,
 * finalizar, abandonar, permisos). Ejecutar: php artisan test --filter=MetasTest
 */
class MetasTest extends TestCase
{
    use RefreshDatabase;

    /** Meta activa con dos participantes ACEPTADOS: [$meta, $ana (creadora), $beto]. */
    private function metaDeDos(int $objetivo = 1000000): array
    {
        $ana  = User::factory()->familiar()->create(['nombre' => 'Ana']);
        $beto = User::factory()->familiar()->create(['nombre' => 'Beto']);
        $meta = Meta::factory()->create(['creador_id' => $ana->id, 'monto_objetivo' => $objetivo, 'compartida' => true]);

        foreach ([$ana, $beto] as $u) {
            ParticipacionMeta::create(['meta_id' => $meta->id, 'user_id' => $u->id,
                'estado_invitacion' => 'aceptada', 'fecha_union' => now()->toDateString()]);
        }

        return [$meta, $ana, $beto];
    }

    private function aporte(Meta $meta, User $u, int $monto): AporteMeta
    {
        return AporteMeta::create(['meta_id' => $meta->id, 'user_id' => $u->id, 'monto' => $monto, 'fecha' => now()->toDateString()]);
    }

    // ------------------------------------------------------------ acceso por rol

    public function test_administrador_recibe_403_en_metas_pero_estandar_no(): void
    {
        $this->actingAs(User::factory()->administrador()->create())->get('/metas')->assertForbidden();

        // El estándar SÍ entra (metas individuales + invitaciones).
        $this->actingAs(User::factory()->create())->get('/metas')->assertOk();
    }

    public function test_sin_sesion_redirige_a_iniciar_sesion(): void
    {
        $this->get('/metas')->assertRedirect('/iniciar-sesion');
    }

    public function test_familiar_ve_el_estado_vacio(): void
    {
        $this->actingAs(User::factory()->familiar()->create())
            ->get('/metas')->assertOk()->assertSee('Aún no tienes metas compartidas');
        $this->get('/metas/invitaciones')->assertOk()->assertSee('Sin invitaciones pendientes');
    }

    // ------------------------------------------------------------ crear meta

    public function test_crear_meta_valida_deja_creadora_aceptada_e_invita(): void
    {
        $ana  = User::factory()->familiar()->create();
        $beto = User::factory()->familiar()->create(['email' => 'beto@test.com']);

        $respuesta = $this->actingAs($ana)->post('/metas', ['compartida' => '1', 
            'nombre' => 'Viaje', 'monto_objetivo' => '$ 4.000.000', 'fecha_limite' => now()->addMonth()->toDateString(),
            'correo' => 'BETO@test.com',
        ]);

        $meta = Meta::firstOrFail();
        $respuesta->assertRedirect(route('metas.show', $meta))->assertSessionHas('exito', 'Meta creada');
        $this->assertSame(4000000, (int) $meta->monto_objetivo);
        $this->assertSame('aceptada', $meta->participaciones()->where('user_id', $ana->id)->value('estado_invitacion'));
        $this->assertSame('pendiente', $meta->participaciones()->where('user_id', $beto->id)->value('estado_invitacion'));
        $this->assertDatabaseHas('notificaciones', ['user_id' => $beto->id, 'tipo' => 'invitacion_meta', 'meta_id' => $meta->id]);
    }

    public function test_crear_meta_invalida(): void
    {
        $ana = User::factory()->familiar()->create();
        User::factory()->familiar()->create(['email' => 'beto@test.com']);

        $this->actingAs($ana);
        $this->post('/metas', ['compartida' => '1', 'nombre' => '', 'monto_objetivo' => '1000', 'correo' => 'beto@test.com'])->assertSessionHasErrors('nombre');
        $this->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '0', 'correo' => 'beto@test.com'])->assertSessionHasErrors('monto_objetivo');
        $this->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '-500', 'correo' => 'beto@test.com'])->assertSessionHasErrors('monto_objetivo');
        $this->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => 'abc', 'correo' => 'beto@test.com'])->assertSessionHasErrors('monto_objetivo');
        $this->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000', 'fecha_limite' => now()->subDay()->toDateString(), 'correo' => 'beto@test.com'])
            ->assertSessionHasErrors('fecha_limite');
        $this->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000'])->assertSessionHasErrors('correo');

        $this->assertSame(0, Meta::count());
    }

    public function test_si_la_invitacion_falla_no_queda_meta_creada(): void
    {
        $ana = User::factory()->familiar()->create();

        $this->actingAs($ana)->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000', 'correo' => 'nadie@test.com'])
            ->assertSessionHasErrors(['correo' => ServicioMetas::MSG_NO_EXISTE]);

        $this->assertSame(0, Meta::count());
        $this->assertSame(0, ParticipacionMeta::count());
    }

    public function test_un_estandar_puede_crear_meta_individual_pero_no_compartida(): void
    {
        $est = User::factory()->create();

        $this->actingAs($est)->post('/metas', ['nombre' => 'Moto', 'monto_objetivo' => '1000'])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, Meta::count());
        $this->assertFalse(Meta::first()->esCompartida());

        $this->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000', 'correo' => 'a@a.com'])
            ->assertSessionHasErrors('compartida');
        $this->assertSame(1, Meta::count());
    }

    // ------------------------------------------------------------ invitar

    public function test_invitar_valida_existencia_duplicado_yo_mismo_y_cupo(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos();
        $estandar = User::factory()->create(['email' => 'est@test.com']);
        $tercero  = User::factory()->familiar()->create(['email' => 'tercero@test.com']);
        $this->actingAs($ana);

        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'noexiste@test.com'])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_NO_EXISTE]);
        $this->post("/metas/{$meta->id}/invitar", ['correo' => $beto->email])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_YA_PARTICIPA]);
        $this->post("/metas/{$meta->id}/invitar", ['correo' => $ana->email])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_YO_MISMO]);
        // La meta ya tiene 2 aceptados: un tercero no cabe (ni familiar ni estándar).
        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'tercero@test.com'])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_LLENA]);
        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'est@test.com'])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_LLENA]);

        $this->assertSame(2, $meta->participaciones()->count());
    }

    public function test_el_cupo_se_reserva_con_la_invitacion_pendiente_y_se_libera_al_rechazar(): void
    {
        $ana  = User::factory()->familiar()->create();
        $beto = User::factory()->familiar()->create(['email' => 'beto@test.com']);
        $caro = User::factory()->familiar()->create(['email' => 'caro@test.com']);

        $this->actingAs($ana)->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000', 'correo' => 'beto@test.com']);
        $meta = Meta::firstOrFail();

        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'caro@test.com'])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_LLENA]);

        $this->actingAs($beto)->post('/metas/invitaciones/'.$meta->participaciones()->where('user_id', $beto->id)->value('id').'/rechazar');

        // Cupo liberado: Caro entra. Beto (rechazó antes) ya no cabe porque el cupo volvió a llenarse.
        $this->actingAs($ana)->post("/metas/{$meta->id}/invitar", ['correo' => 'caro@test.com'])->assertSessionHasNoErrors();
        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'beto@test.com'])->assertSessionHasErrors(['correo' => ServicioMetas::MSG_LLENA]);
    }

    public function test_solo_la_creadora_invita(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos();
        User::factory()->familiar()->create(['email' => 'caro@test.com']);

        $this->actingAs($beto)->post("/metas/{$meta->id}/invitar", ['correo' => 'caro@test.com'])->assertForbidden();
    }

    // ------------------------------------------------------------ aceptar / rechazar

    private function invitacionPendiente(): array
    {
        $ana  = User::factory()->familiar()->create(['nombre' => 'Ana']);
        $beto = User::factory()->familiar()->create(['nombre' => 'Beto', 'email' => 'beto@test.com']);
        $this->actingAs($ana)->post('/metas', ['compartida' => '1', 'nombre' => 'Casa', 'monto_objetivo' => '1000000', 'correo' => 'beto@test.com']);
        $meta = Meta::firstOrFail();
        $part = $meta->participaciones()->where('user_id', $beto->id)->firstOrFail();

        return [$meta, $ana, $beto, $part];
    }

    public function test_el_invitado_ve_y_acepta_la_invitacion(): void
    {
        [$meta, $ana, $beto, $part] = $this->invitacionPendiente();

        $this->actingAs($beto)->get('/metas/invitaciones')->assertOk()->assertSee('Casa');
        $this->post("/metas/invitaciones/{$part->id}/aceptar")
            ->assertRedirect(route('metas.show', $meta))->assertSessionHas('exito', 'Invitación aceptada');

        $this->assertSame('aceptada', $part->fresh()->estado_invitacion);
        $this->assertNotNull($part->fresh()->fecha_union);
        $this->assertDatabaseHas('notificaciones', ['user_id' => $ana->id, 'tipo' => 'respuesta_meta']);
    }

    public function test_el_invitado_rechaza_la_invitacion(): void
    {
        [$meta, $ana, $beto, $part] = $this->invitacionPendiente();

        $this->actingAs($beto)->post("/metas/invitaciones/{$part->id}/rechazar")
            ->assertRedirect(route('metas.invitaciones'))->assertSessionHas('exito', 'Invitación rechazada');

        $this->assertSame('rechazada', $part->fresh()->estado_invitacion);
        $this->assertDatabaseHas('notificaciones', ['user_id' => $ana->id, 'tipo' => 'respuesta_meta']);
        $this->get('/metas/invitaciones')->assertSee('Sin invitaciones pendientes');
    }

    public function test_solo_el_invitado_puede_responder_la_invitacion(): void
    {
        [$meta, $ana, $beto, $part] = $this->invitacionPendiente();
        $ajeno = User::factory()->familiar()->create();

        // La creadora y un tercero NO pueden responder por Beto.
        $this->actingAs($ana)->post("/metas/invitaciones/{$part->id}/aceptar")->assertForbidden();
        $this->actingAs($ajeno)->post("/metas/invitaciones/{$part->id}/aceptar")->assertForbidden();
        $this->actingAs($ajeno)->post("/metas/invitaciones/{$part->id}/rechazar")->assertForbidden();
        $this->assertSame('pendiente', $part->fresh()->estado_invitacion);

        // Y no se puede aceptar dos veces.
        $this->actingAs($beto)->post("/metas/invitaciones/{$part->id}/aceptar");
        $this->post("/metas/invitaciones/{$part->id}/aceptar")->assertForbidden();
    }

    public function test_un_invitado_pendiente_aun_no_ve_la_meta(): void
    {
        [$meta, , $beto] = $this->invitacionPendiente();

        $this->actingAs($beto)->get("/metas/{$meta->id}")->assertForbidden();
    }

    // ------------------------------------------------------------ aportes y avance

    public function test_aporte_valido_y_calculo_del_avance(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos(1000000);

        $this->actingAs($ana)->post("/metas/{$meta->id}/aportes", ['monto' => '250.000'])
            ->assertRedirect(route('metas.show', $meta))->assertSessionHas('exito', 'Aporte registrado');
        $this->actingAs($beto)->post("/metas/{$meta->id}/aportes", ['monto' => '250000']);

        $avance = app(ServicioMetas::class)->avance($meta);
        $this->assertSame(500000, $avance['total']);
        $this->assertSame(50, $avance['porcentaje']);
        $this->assertSame(500000, $avance['falta']);
        $this->assertDatabaseHas('aportes_meta', ['meta_id' => $meta->id, 'user_id' => $ana->id, 'monto' => 250000]);
    }

    public function test_aporte_cero_negativo_o_texto_se_rechaza(): void
    {
        [$meta, $ana] = $this->metaDeDos();
        $this->actingAs($ana);

        foreach (['0', '-100', 'abc', '', '12.5x'] as $malo) {
            $this->post("/metas/{$meta->id}/aportes", ['monto' => $malo])->assertSessionHasErrors('monto');
        }
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '1000', 'fecha' => now()->addDay()->toDateString()])->assertSessionHasErrors('fecha');
        $this->assertSame(0, AporteMeta::count());
    }

    public function test_no_se_aporta_a_una_meta_inactiva(): void
    {
        [$meta, $ana] = $this->metaDeDos();
        $meta->update(['estado' => 'cancelada']);

        $this->actingAs($ana)->post("/metas/{$meta->id}/aportes", ['monto' => '1000'])->assertForbidden();
        $this->assertSame(0, AporteMeta::count());
    }

    public function test_el_porcentaje_tiene_tope_de_100(): void
    {
        [$meta, $ana] = $this->metaDeDos(1000);
        $this->aporte($meta, $ana, 5000);

        $this->assertSame(100, $meta->porcentaje());
    }

    // ------------------------------------------------------------ PRIVACIDAD (RQNF-03)

    public function test_cada_uno_ve_su_aporte_y_el_total_pero_nunca_el_del_otro(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos(20000000);
        $this->aporte($meta, $ana, 100000);
        $this->aporte($meta, $beto, 7654321);          // cifra distintiva: no debe aparecer en la vista de Ana

        $respuesta = $this->actingAs($ana)->get("/metas/{$meta->id}")->assertOk();

        $respuesta->assertSee('7.754.321');            // total conjunto SÍ
        $respuesta->assertSee('100.000');              // su propio aporte SÍ
        $respuesta->assertDontSee('7.654.321');        // el de Beto NO (formateado)
        $respuesta->assertDontSee('7654321');          // ni "escondido" en el HTML
        $this->assertTrue($respuesta->viewData('aportes')->every(fn ($a) => $a->user_id === $ana->id));
        $this->assertCount(1, $respuesta->viewData('aportes'));

        // Y a la inversa.
        $this->actingAs($beto)->get("/metas/{$meta->id}")->assertOk()->assertSee('7.654.321')->assertDontSee('100.000');
    }

    public function test_el_listado_muestra_nombres_y_total_pero_ningun_aporte_individual(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos(20000000);
        $this->aporte($meta, $beto, 7654321);

        $this->actingAs($ana)->get('/metas')->assertOk()
            ->assertSee('Beto')->assertSee('7.654.321')   // aquí coincide con el TOTAL (solo aportó Beto): es el conjunto
            ->assertDontSee('"user_id"');
        $this->aporte($meta, $ana, 111111);
        // Con dos aportes, ninguna de las dos cifras sueltas aparece: solo la suma.
        $this->get('/metas')->assertSee('7.765.432')->assertDontSee('7.654.321')->assertDontSee('111.111');
    }

    public function test_las_politicas_de_aporte_son_solo_del_dueno(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos();
        $aporteDeBeto = $this->aporte($meta, $beto, 5000);

        $this->assertTrue($beto->can('view', $aporteDeBeto));
        $this->assertFalse($ana->can('view', $aporteDeBeto));          // aunque comparta la meta
        $this->assertFalse($beto->can('update', $aporteDeBeto));
        $this->assertFalse($beto->can('delete', $aporteDeBeto));
    }

    public function test_c6_aportes_del_usuario_en_un_periodo(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos();
        AporteMeta::create(['meta_id' => $meta->id, 'user_id' => $ana->id, 'monto' => 100, 'fecha' => '2026-09-10']);
        AporteMeta::create(['meta_id' => $meta->id, 'user_id' => $ana->id, 'monto' => 300, 'fecha' => '2026-10-05']);
        AporteMeta::create(['meta_id' => $meta->id, 'user_id' => $beto->id, 'monto' => 999, 'fecha' => '2026-10-06']);

        $octubre = $meta->aportesDelUsuario($ana->id, \Carbon\Carbon::parse('2026-10-01'), \Carbon\Carbon::parse('2026-10-31'))->sum('monto');
        $this->assertSame(300, (int) $octubre);
    }

    // ------------------------------------------------------------ ajenos

    public function test_un_ajeno_recibe_403_en_todas_las_rutas_de_la_meta(): void
    {
        [$meta] = $this->metaDeDos();
        $this->aporte($meta, $meta->creador, 5000);
        $ajeno = User::factory()->familiar()->create(['email' => 'ajeno@test.com']);
        $this->actingAs($ajeno);

        $this->get("/metas/{$meta->id}")->assertForbidden();
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '1000'])->assertForbidden();
        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'x@x.com'])->assertForbidden();
        $this->post("/metas/{$meta->id}/finalizar")->assertForbidden();
        $this->post("/metas/{$meta->id}/abandonar")->assertForbidden();
        $this->get('/metas')->assertOk()->assertDontSee($meta->nombre);   // ni en su listado
        $this->assertSame(1, AporteMeta::count());
    }

    // ------------------------------------------------------------ finalizar y abandonar

    public function test_finalizar_solo_al_llegar_al_objetivo(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos(1000);
        $this->aporte($meta, $ana, 400);

        $this->actingAs($ana)->post("/metas/{$meta->id}/finalizar")->assertSessionHasErrors('meta');
        $this->assertSame('activa', $meta->fresh()->estado);

        $this->aporte($meta, $beto, 700);   // 1100 >= 1000
        $this->post("/metas/{$meta->id}/finalizar")->assertRedirect()->assertSessionHas('exito', 'Meta finalizada');

        $this->assertSame('completada', $meta->fresh()->estado);
        $this->assertDatabaseHas('notificaciones', ['user_id' => $beto->id, 'tipo' => 'meta_finalizada']);
        // Ya completada: no recibe más aportes.
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '1'])->assertForbidden();
    }

    public function test_abandonar_cancela_la_meta_conserva_aportes_y_notifica(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos();
        $this->aporte($meta, $ana, 5000);

        $this->actingAs($beto)->post("/metas/{$meta->id}/abandonar")
            ->assertRedirect(route('metas.index'))->assertSessionHas('exito', 'Meta abandonada');

        $this->assertSame('cancelada', $meta->fresh()->estado);
        $this->assertSame('abandonada', $meta->participaciones()->where('user_id', $beto->id)->value('estado_invitacion'));
        $this->assertSame(1, AporteMeta::count());                              // historial intacto
        $this->assertDatabaseHas('notificaciones', ['user_id' => $ana->id, 'tipo' => 'meta_abandonada']);
        $this->actingAs($beto)->get("/metas/{$meta->id}")->assertForbidden();   // quien abandonó ya no la ve
        $this->actingAs($ana)->get("/metas/{$meta->id}")->assertOk();           // la otra persona sí (solo lectura)
    }


    // ------------------------------------------------------------ metas individuales

    public function test_crear_meta_individual_no_invita_a_nadie(): void
    {
        $ana = User::factory()->familiar()->create();

        // Casilla "compartir" sin marcar y sin correo.
        $this->actingAs($ana)->post('/metas', ['nombre' => 'Moto', 'monto_objetivo' => '3000000'])
            ->assertSessionHasNoErrors();

        $meta = Meta::firstOrFail();
        $this->assertFalse($meta->esCompartida());
        $this->assertSame(1, $meta->participaciones()->count());
        $this->assertSame($ana->id, (int) $meta->participaciones()->value('user_id'));
        $this->assertSame(0, Notificacion::count());
    }

    public function test_en_una_meta_individual_se_ignora_el_correo(): void
    {
        $ana = User::factory()->familiar()->create();
        User::factory()->familiar()->create(['email' => 'beto@test.com']);

        $this->actingAs($ana)->post('/metas', ['nombre' => 'Moto', 'monto_objetivo' => '3000000', 'correo' => 'beto@test.com'])
            ->assertSessionHasNoErrors();

        $meta = Meta::firstOrFail();
        $this->assertFalse($meta->esCompartida());
        $this->assertSame(1, $meta->participaciones()->count());   // Beto NO fue invitado
        $this->assertSame(0, Notificacion::count());
    }

    public function test_si_se_comparte_el_correo_es_obligatorio(): void
    {
        $this->actingAs(User::factory()->familiar()->create())
            ->post('/metas', ['compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000'])
            ->assertSessionHasErrors('correo');
        $this->assertSame(0, Meta::count());
    }

    public function test_una_meta_individual_no_admite_invitaciones(): void
    {
        $ana = User::factory()->familiar()->create();
        User::factory()->familiar()->create(['email' => 'beto@test.com']);
        $this->actingAs($ana)->post('/metas', ['nombre' => 'Moto', 'monto_objetivo' => '3000000']);
        $meta = Meta::firstOrFail();

        $this->post("/metas/{$meta->id}/invitar", ['correo' => 'beto@test.com'])->assertForbidden();
        $this->assertSame(1, $meta->participaciones()->count());

        // Tampoco por la vía del servicio.
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(ServicioMetas::class)->invitar($meta, 'beto@test.com');
    }

    public function test_meta_individual_aportes_avance_finalizar_y_cancelar(): void
    {
        $ana = User::factory()->familiar()->create();
        $this->actingAs($ana)->post('/metas', ['nombre' => 'Moto', 'monto_objetivo' => '1000']);
        $meta = Meta::firstOrFail();

        $this->post("/metas/{$meta->id}/aportes", ['monto' => '400'])->assertSessionHas('exito', 'Aporte registrado');
        $this->assertSame(40, $meta->fresh()->porcentaje());
        $this->get("/metas/{$meta->id}")->assertOk()->assertSee('Meta individual')->assertDontSee('Enviar invitación');

        $this->post("/metas/{$meta->id}/finalizar")->assertSessionHasErrors('meta');
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '600']);
        $this->post("/metas/{$meta->id}/finalizar")->assertSessionHas('exito', 'Meta finalizada');
        $this->assertSame('completada', $meta->fresh()->estado);
    }

    public function test_cancelar_una_meta_individual(): void
    {
        $ana = User::factory()->familiar()->create();
        $this->actingAs($ana)->post('/metas', ['nombre' => 'Moto', 'monto_objetivo' => '1000']);
        $meta = Meta::firstOrFail();
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '100']);

        $this->post("/metas/{$meta->id}/abandonar")->assertRedirect(route('metas.index'))->assertSessionHas('exito', 'Meta cancelada');

        $this->assertSame('cancelada', $meta->fresh()->estado);
        $this->assertSame(1, AporteMeta::count());   // el historial se conserva
    }

    public function test_una_meta_individual_es_privada_para_otros_familiares(): void
    {
        $ana = User::factory()->familiar()->create();
        $otra = User::factory()->familiar()->create();
        $this->actingAs($ana)->post('/metas', ['nombre' => 'Secreta', 'monto_objetivo' => '1000']);
        $meta = Meta::firstOrFail();
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '777']);

        $this->actingAs($otra)->get("/metas/{$meta->id}")->assertForbidden();
        $this->post("/metas/{$meta->id}/aportes", ['monto' => '5'])->assertForbidden();
        $this->get('/metas')->assertOk()->assertDontSee('Secreta');
        $this->assertSame(1, AporteMeta::count());
    }

    public function test_el_listado_distingue_individuales_y_compartidas(): void
    {
        [$compartida, $ana] = $this->metaDeDos();
        $compartida->update(['compartida' => true, 'nombre' => 'Casa juntos']);
        $this->actingAs($ana)->post('/metas', ['nombre' => 'Moto propia', 'monto_objetivo' => '1000']);

        $this->get('/metas')->assertOk()->assertSee('Casa juntos')->assertSee('Moto propia')
            ->assertSee('Compartida')->assertSee('Individual');
    }

    // ------------------------------------------------------------ contrato C6

    public function test_tiene_metas_activas(): void
    {
        [$meta, $ana, $beto] = $this->metaDeDos();
        $otro = User::factory()->familiar()->create();
        $servicio = app(ServicioMetas::class);

        // Una meta individual activa también cuenta (el estándar no puede usar metas).
        $this->actingAs($otro)->post('/metas', ['nombre' => 'Solo mía', 'monto_objetivo' => '1000']);
        $this->assertTrue($servicio->tieneMetasActivas($otro));
        Meta::where('creador_id', $otro->id)->update(['estado' => 'cancelada']);

        $this->assertTrue($servicio->tieneMetasActivas($ana));
        $this->assertTrue($servicio->tieneMetasActivas($beto));
        $this->assertFalse($servicio->tieneMetasActivas($otro));

        $meta->update(['estado' => 'completada']);
        $this->assertFalse($servicio->tieneMetasActivas($ana));
    }

        public function test_invitar_a_un_estandar_crea_invitacion_y_notificacion_especial(): void
    {
        $ana = User::factory()->familiar()->create();
        $est = User::factory()->create(['email' => 'est@test.com']);

        $this->actingAs($ana)->post('/metas', ['compartida' => '1', 'nombre' => 'Viaje', 'monto_objetivo' => '1000', 'correo' => 'est@test.com']);

        $meta = Meta::firstOrFail();
        $this->assertDatabaseHas('participaciones_meta', [
            'meta_id' => $meta->id, 'user_id' => $est->id, 'estado_invitacion' => 'pendiente',
        ]);
        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $est->id, 'tipo' => 'invitacion_meta_requiere_familiar', 'meta_id' => $meta->id,
        ]);
        $this->assertDatabaseMissing('notificaciones', [
            'user_id' => $est->id, 'tipo' => 'invitacion_meta',
        ]);
    }

    public function test_estandar_no_puede_aceptar_hasta_cambiar_a_familiar(): void
    {
        $ana = User::factory()->familiar()->create();
        $est = User::factory()->create(['email' => 'est@test.com']);

        $this->actingAs($ana)->post('/metas', ['compartida' => '1', 'nombre' => 'Viaje', 'monto_objetivo' => '1000', 'correo' => 'est@test.com']);
        $meta = Meta::firstOrFail();
        $part = $meta->participaciones()->where('user_id', $est->id)->firstOrFail();

        // Intenta aceptar siendo estándar: error y sigue pendiente.
        $this->actingAs($est)->post("/metas/invitaciones/{$part->id}/aceptar")
            ->assertSessionHasErrors('invitacion');
        $this->assertSame('pendiente', $part->fresh()->estado_invitacion);

        // Cambia a familiar desde Ajustes.
        $this->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'familiar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));

        // Ahora sí acepta.
        $this->post("/metas/invitaciones/{$part->id}/aceptar")
            ->assertRedirect(route('metas.show', $meta));
        $this->assertSame('aceptada', $part->fresh()->estado_invitacion);
    }
}
