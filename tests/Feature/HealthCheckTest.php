<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_endpoint_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200)
                 ->assertSee('AntriSehat');
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
}
