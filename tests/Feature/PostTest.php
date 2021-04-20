<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorised_users_can_create_a_post()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->postJson('/api/post', [
            'title' => 'Laravel',
            'body' => 'My first laravel project'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Laravel',
            'body' => 'My first laravel project'
        ]);
    }

    /** @test */
    public function unauthorised_users_cannot_create_a_post()
    {
        $response = $this->postJson('/api/post', [
            'title' => 'Laravel',
            'body' => 'My first laravel project'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function all_users_cannot_create_a_post_with_an_existing_title()
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->postJson('/api/post', [
            'title' => $post->title,
            'body' => 'My first laravel project'
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function all_users_can_see_all_the_posts_created()
    {
        Post::factory()->count(5)->create();

        $response = $this->get('/api/post');

        $response->assertStatus(200);
    }

    /** @test */
    public function all_users_can_see_a_certain_post_from_its_id()
    {
        $post = Post::factory()->create();

        $response = $this->get('/api/post/' . $post->id);

        $response->assertStatus(200);
    }

    /** @test */
    public function all_users_get_a_404_if_post_does_not_exist()
    {
        Post::factory()->create();

        $response = $this->get('/api/post/' . 1);

        $response->assertStatus(404);
    }

    /** @test */
    public function authorised_users_can_update_their_own_posts()
    {
        $post = Post::factory()->create();
        $user = $post->user;

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->putJson('/api/post/' . $post->id, [
            'title' => 'New Post',
            'body' => 'Just updated post!'
        ]);

        $response->assertStatus(202);

        $post->refresh();

        $this->assertEquals('New Post', $post->title);
    }

    /** @test */
    public function authorised_users_cannot_update_other_peoples_posts()
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->putJson('/api/post/' . $post->id, [
            'title' => 'New Post',
            'body' => 'Just updated post!'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function authorised_users_can_delete_their_own_posts()
    {
        $post = Post::factory()->create();
        $user = $post->user;

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->delete('/api/post/' . $post->id);

        $response->assertStatus(202);
    }

    /** @test */
    public function authorised_users_cannot_delete_other_peoples_posts()
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user, ['*']
        );

        $response = $this->delete('/api/post/' . $post->id);

        $response->assertStatus(401);
    }

    /** @test */
    public function authorised_users_can_attach_tags_to_their_post()
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

        $this->assertDatabaseHas('posts_tags_link', [
            'post_id' => $post->id,
            'tag_id' => $tag->id
        ]);
    }
}
