<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_home_endpoint_returns_200(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'running']);
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
