<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_portal_accessible_without_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200)
                 ->assertSee('Registrasi Mandiri Pasien');
    }

    public function test_health_endpoint_returns_200(): void
    {
        $response = $this->get('/health');
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'healthy',
                     'database' => 'connected',
                 ]);
    }

    public function test_doctor_and_operator_routes_require_role_based_login(): void
    {
        (new DatabaseSeeder())->run();

        // Guest dialihkan ke /login
        $this->get('/dokter')->assertRedirect('/login');
        $this->get('/operator')->assertRedirect('/login');

        // Dokter bisa buka /dokter tapi ditolak (403) saat buka /operator
        $dokter = User::where('role', 'dokter')->first();
        $this->actingAs($dokter)->get('/dokter')->assertStatus(200);
        $this->actingAs($dokter)->get('/operator')->assertStatus(403);

        // Operator bisa buka /operator tapi ditolak (403) saat buka /dokter
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator)->get('/operator')->assertStatus(200);
        $this->actingAs($operator)->get('/dokter')->assertStatus(403);
    }
}
