<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadisticasTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_estadisticas(): void
    {
        $admin = User::factory()->administrador()->create();
        User::factory()->count(5)->create();

        $this->actingAs($admin)->get(route('admin.estadisticas'))->assertOk();
    }

    public function test_no_admin_recibe_403(): void
    {
        $user = User::factory()->familiar()->create();
        $this->actingAs($user)->get(route('admin.estadisticas'))->assertForbidden();
    }

    public function test_sin_sesion_redirige(): void
    {
        $this->get(route('admin.estadisticas'))->assertRedirect(route('login'));
    }
}