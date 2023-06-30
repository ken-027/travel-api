<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $api = '/api/v1/auth';

    /**
     * A basic feature test example.
     */
    public function test_login_returns_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson("$this->api/login", [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    public function test_login_returns_errors_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson("$this->api/login", [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertSeeText('invalid credentials!');
    }

    public function test_login_returns_errors_validations(): void
    {
        $response = $this->postJson("$this->api/login", [
            'email' => 'sample@email.com',
            'password' => 'pass',
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['password']]);

        $response = $this->postJson("$this->api/login", [
            'email' => 'email@',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['email']]);
    }
}
