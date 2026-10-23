<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_customer_and_ignores_role_input(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'secret123',
            'role' => 'admin', // harus diabaikan
        ]);

        $response->assertCreated()->assertJsonPath('user.role', 'customer');
        $this->assertSame('customer', User::where('email', 'budi@example.com')->value('role'));
    }

    public function test_login_returns_bearer_token_and_profile_works_with_it(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        $login = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret123'])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $user->email);

        $this->withToken($login->json('token'))
            ->getJson('/api/profile')
            ->assertOk()
            ->assertJsonPath('email', $user->email);
    }

    public function test_login_with_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_protected_endpoint_without_token_returns_json_401(): void
    {
        // Tanpa header Accept pun harus JSON 401, bukan redirect ke login Blade
        $this->get('/api/profile')->assertUnauthorized();
    }

    public function test_logout_revokes_the_token(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);
        $token = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret123'])->json('token');

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
