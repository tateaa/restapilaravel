<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostTest extends TestCase
{
    // ── INDEX ──────────────────────────────────────────────────────
    public function test_returns_paginated_posts_without_auth(): void
    {
        Post::factory(30)->create();

        $response = $this->getJson('/api/posts');
        
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'title', 'body', 'status']],
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
                'links',
            ]);
    }

    public function test_respects_per_page_parameter_with_max_cap(): void
    {
        Post::factory(150)->create();

        $response = $this->getJson('/api/posts?per_page=200');

        $response->assertOk();
        $this->assertLessThanOrEqual(100, $response->json('meta.per_page'));
    }

    // ── STORE ──────────────────────────────────────────────────────
    public function test_allows_editor_to_create_post(): void
    {
        $editor = User::factory()->create()->assignRole('editor');

        $response = $this->actingAs($editor)
            ->postJson('/api/posts', [
                'title'   => 'Post Baru',
                'status'  => PostStatus::Draft->value,
                'content' => 'Konten yang cukup panjang untuk divalidasi.',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.body', 'Konten yang cukup panjang untuk divalidasi.');
    }

    public function test_prevents_reader_from_creating_post(): void
    {
        $reader = User::factory()->create()->assignRole('reader');

        $response = $this->actingAs($reader)
            ->postJson('/api/posts', ['title' => 'Test', 'status' => 'draft', 'content' => 'isi']);

        $response->assertForbidden();
    }

    public function test_sets_user_id_from_token_not_from_input(): void
    {
        $editor = User::factory()->create()->assignRole('editor');
        $other  = User::factory()->create();

        $response = $this->actingAs($editor)
            ->postJson('/api/posts', [
                'title'   => 'Test IDOR',
                'status'  => 'draft',
                'content' => 'Konten untuk test IDOR keamanan.',
                'user_id' => $other->id,
            ]);

        $response->assertCreated();
        $this->assertEquals($editor->id, $response->json('data.author.id'));
    }

    // ── DESTROY ────────────────────────────────────────────────────
    public function test_allows_admin_to_delete_any_post(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $post  = Post::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertNoContent();
    }

    public function test_prevents_editor_from_deleting_post(): void
    {
        $editor = User::factory()->create()->assignRole('editor');
        $post   = Post::factory()->create();

        $response = $this->actingAs($editor)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertForbidden();
    }

    // ── ERROR HANDLING ─────────────────────────────────────────────
    public function test_returns_404_for_missing_post(): void
    {
        $response = $this->getJson('/api/posts/99999');

        $response->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function test_returns_422_for_invalid_input(): void
    {
        $admin = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($admin)
            ->postJson('/api/posts', []);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['title', 'status', 'content']]);
    }

    // ── SECURITY ───────────────────────────────────────────────────
    public function test_rejects_token_in_url_query_string(): void
    {
        $response = $this->getJson('/api/posts?token=abc123');

        $response->assertStatus(400)
            ->assertJsonPath('message', fn($msg) => str_contains($msg, 'URL'));
    }
}

