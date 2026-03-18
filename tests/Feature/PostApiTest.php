<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class PostApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_list_all_posts(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']); 
    }

    public function test_unauthenticated_user_cannot_create_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Unauth Title',
            'content' => 'Some content'
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/posts', [
                             'title' => 'New Test Post',
                             'content' => 'This is a test content'
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'New Test Post');
    }

    public function test_user_cannot_update_others_post(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($stranger, 'sanctum')
                         ->putJson("/api/posts/{$post->id}", [
                             'title' => 'Updated Title',
                             'content' => 'Updated content'
                         ]);

        $response->assertStatus(403);
    }
}
