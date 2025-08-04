<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ForumPost;
use App\Enums\RoleEnum;
use App\Models\ForumComment;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForumControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_create_post()
    {
        $user = User::factory()->create(['role' => RoleEnum::STUDENT]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/forum', [
                'title' => 'Test Post',
                'body' => 'This is a test post.',
                'tags' => ['tech', 'career'],
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('forum_posts', ['title' => 'Test Post']);
    }

    public function test_mentor_can_pin_post()
    {
        $mentor = User::factory()->create(['role' => RoleEnum::MENTOR]);
        $token = JWTAuth::fromUser($mentor);
        $post = ForumPost::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/forum/{$post->uuid}/pin");

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_posts', ['uuid' => $post->uuid, 'pinned' => true]);
    }

    public function test_admin_can_moderate_post()
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        $token = JWTAuth::fromUser($admin);
        $post = ForumPost::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/forum/{$post->uuid}/moderate", ['status' => 'hidden']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forum_posts', ['uuid' => $post->uuid, 'status' => 'hidden']);
    }

    public function test_admin_can_list_comments()
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN]);
        ForumComment::factory()->count(3)->create(['status' => 'active']);
        ForumComment::factory()->create(['status' => 'hidden']);

        $response = $this->actingAs($admin, 'api')->getJson('/api/forum/comments?status=active');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'comments');
    }
}