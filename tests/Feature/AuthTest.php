<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // ── LOGIN ──────────────────────────────────────────────────────
    public function test_allows_user_to_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com', 'password' => Hash::make('password')]);
        $user->assignRole('editor');

        $response = $this->postJson('/api/login', ['email' => 'test@example.com', 'password' => 'password']);

        $response->assertOk()
            ->assertJsonStructure(['token', 'expires_at', 'role', 'abilities']);
    }

    public function test_returns_401_for_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'test@example.com', 'password' => Hash::make('password')]);

        $response = $this->postJson('/api/login', ['email' => 'test@example.com', 'password' => 'wrong']);

        $response->assertUnauthorized()
            ->assertJsonPath('message', fn($msg) => str_contains($msg, 'Kredensial'));
    }

    public function test_returns_401_for_non_existent_email(): void
    {
        $response = $this->postJson('/api/login', ['email' => 'nonexistent@example.com', 'password' => 'password']);

        $response->assertUnauthorized();
    }

    public function test_token_has_correct_abilities_based_on_role(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => Hash::make('password')]);
        $admin->assignRole('admin');

        $response = $this->postJson('/api/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertOk();

        $this->assertContains('posts:delete', $response->json('abilities'));
        $this->assertContains('comments:delete', $response->json('abilities'));
    }

    // ── LOGOUT ─────────────────────────────────────────────────────
    public function test_allows_authenticated_user_to_logout(): void
    {
        $user = User::factory()->create()->assignRole('reader');
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertOk();
    }

    public function test_returns_401_for_logout_without_token(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertUnauthorized();
    }

    // ── ME ─────────────────────────────────────────────────────────
    public function test_returns_user_info_for_me_endpoint(): void
    {
        $user = User::factory()->create(['name' => 'Test User'])->assignRole('editor');

        $response = $this->actingAs($user)
            ->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('name', 'Test User')
            ->assertJsonPath('role', 'editor');
    }

    public function test_returns_401_for_me_without_token(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }
}

