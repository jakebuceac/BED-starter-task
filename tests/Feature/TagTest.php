<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorised_users_can_create_a_tag()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->postJson('/api/tag', [
            'name' => 'Laravel',
        ]);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'name' => 'Laravel',
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Laravel',
        ]);
    }

    /** @test */
    public function unauthorised_users_cannot_create_a_tag()
    {
        $response = $this->postJson('/api/tag', [
            'name' => 'Laravel',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function authorised_users_cannot_create_a_tag_that_already_exists()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->postJson('/api/tag', [
            'name' => $tag->name,
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function all_users_can_see_all_the_tags_created()
    {
        Tag::factory()->count(5)->create();

        $response = $this->get('/api/tag');

        $response->assertStatus(200);
    }

    /** @test */
    public function all_users_can_see_a_certain_tag_from_its_id()
    {
        $tag = Tag::factory()->create();

        $response = $this->get('/api/tag/' . $tag->id);

        $response->assertStatus(200);
    }

    /** @test */
    public function all_users_get_a_404_if_tag_does_not_exist()
    {
        Tag::factory()->create();

        $response = $this->get('/api/tag/' . 1);

        $response->assertStatus(404);
    }

    /** @test */
    public function authorised_users_can_update_a_tag_when_it_is_not_used_by_a_post()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->putJson('/api/tag/' . $tag->id, [
            'name' => 'Laravel',
        ]);

        $response->assertStatus(202);

        $tag->refresh();

        $this->assertEquals('Laravel', $tag->name);
    }

    /** @test */
    public function authorised_users_cannot_update_a_tag_when_it_is_being_used_by_a_post()
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();
        $user = $post->user;

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->postJson('/api/post/' . $post->id . '/attach', [
            'tag_id' => $tag->id
        ]);

        $response->assertStatus(200);

        $response = $this->putJson('/api/tag/' . $tag->id, [
            'name' => 'Laravel',
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function authorised_users_can_delete_a_tag_when_it_is_not_used_by_a_post()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->delete('/api/tag/' . $tag->id);

        $response->assertStatus(202);
    }

    /** @test */
    public function authorised_users_cannot_delete_a_tag_when_it_is_being_used_by_a_post()
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();
        $user = $post->user;

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->postJson('/api/post/' . $post->id . '/attach', [
            'tag_id' => $tag->id
        ]);

        $response->assertStatus(200);

        $response = $this->delete('/api/tag/' . $tag->id);

        $response->assertStatus(405);
    }
}
