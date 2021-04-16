<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function all_users_can_see_all_the_users_that_have_registered()
    {
        User::factory()->count(5)->create();

        $response = $this->get('/api/tag');

        $response->assertStatus(200);
    }
}
