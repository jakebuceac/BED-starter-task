<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register_a_login()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'test1'
        ]);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'name' => 'Test',
            'email' => 'test@gmail.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test',
            'email' => 'test@gmail.com',
        ]);
    }

    /** @test */
    public function a_user_cannot_register_again_with_the_same_details()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password
        ]);

        $response->assertStatus(405);
    }

    /** @test */
    public function a_user_can_login_once_they_have_registered()
    {
        $user = User::factory()->create();

        $user->password = Hash::make('test');
        $user->save();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'test',
            'device_name' => 'Desktop App'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);
    }

    /** @test */
    public function a_user_who_is_not_registered_cannot_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@gmail.com',
            'password' => 'test',
            'device_name' => 'Desktop App'
        ]);


        $response->assertStatus(405);
    }
}
