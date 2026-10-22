<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PanelTest — admin.panel.
 *
 * Verifica:
 *  - admin accede (200)
 *  - estandar y familiar reciben 403
 *  - invitado es redirigido al login
 *  - el panel NO expone datos financieros (ingreso_mensual, gastos)
 */
class PanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_puede_ver_el_panel(): void
    {
        $admin = User::factory()->administrador()->create();

        $response = $this->actingAs($admin)->get(route('admin.panel'));

        $response->assertOk();
        $response->assertSee('Panel de administración');
        $response->assertSee('Distribución por rol');
        $response->assertSee('Últimos usuarios registrados');
    }

    public function test_estandar_recibe_403(): void
    {
        $estandar = User::factory()->create(); // rol estandar por defecto

        $this->actingAs($estandar)
            ->get(route('admin.panel'))
            ->assertForbidden();
    }

    public function test_familiar_recibe_403(): void
    {
        $familiar = User::factory()->familiar()->create();

        $this->actingAs($familiar)
            ->get(route('admin.panel'))
            ->assertForbidden();
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('admin.panel'))
            ->assertRedirect(route('login'));
    }

    public function test_el_panel_no_expone_datos_financieros(): void
    {
        // Creamos un usuario con ingreso y gastos, y verificamos que nada de eso
        // aparezca en el HTML del panel.
        $estandar = User::factory()->create([
            'ingreso_mensual' => 1234567,
        ]);
        $admin = User::factory()->administrador()->create();

        $response = $this->actingAs($admin)->get(route('admin.panel'));

        $response->assertOk();

        // El ingreso del usuario estándar NUNCA debe aparecer en el HTML.
        $response->assertDontSee('1234567');
        $response->assertDontSee('1.234.567');

        // Y no debe haber enlaces a datos financieros de terceros.
        $response->assertDontSee(route('gastos.index'));
        $response->assertDontSee(route('informes.actual'));
        $response->assertDontSee(route('creditos.historial'));
    }
}