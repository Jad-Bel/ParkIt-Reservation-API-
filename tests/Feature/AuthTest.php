<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_register_user(): void
    {
        $userData = [
            'name' => 'test',
            'email' => 'random@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'random@example.com',
        ]);

        $user = User::where('email', 'random@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_create_user_validation(): void
    {
        $userData = [
            'name' => 'test',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'email',
            'password'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'random@example.com',
        ]);

        $user = User::where('email', 'random@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_create_user_duplicate_email(): void
    {
        User::create([
            'name' => 'test',
            'email' => 'random@example.com',
            'password' => Hash::make('password123')
        ]);

        $userData = [
            'name' => 'test',
            'email' => 'random@example.com',
            'password123'
        ];
        $response = $this->postJson('/api/register', $userData);

        $response->asserStatus(422);
    }

    public function test_login_user(): void
    {
        $user = User::create([
            'name' => 'test',
            'email' => 'youness@example.com',
            'password' => Hash::make('password123')
        ]);
        $userData = [
            'email' => $user->email,
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                // 'status',
                'message',
                // 'data',
            ]);
    }

    public function test_login_with_invalide_credentials(): void
    {
        $userData = [
            'email' => 'taha@example.com',
        ];

        $response = $this->postJson('/api/login', $userData);

        $response->assertStatus(422);
    }

    public function test_login_with_invalid_password(): void
    {
        $userData = [
            'email' => 'taha@example.com',
            'password' => 'PASSWORD'
        ];

        $response = $this->postJson('/api/login', $userData);

        $response->assertStatus(401);
    }

    public function test_logout_user(): void
    {
        // $userData = [
        //     'email' => 'taha@example.com',
        //     'password' => 'password123'
        // ];
        /*$userData = User::factory()->create([
            'email' => 'hassan6@example.com',
            'password' => Hash::make('password123')
        ]);*/

        $loginData = [
            'email' => 'john.doe@example.com',
            'password' => 'password123'
        ];



        $tokenResponse = $this->postJson('/api/login', $loginData);
        $token = $tokenResponse['token'];

        $headers = [
            'Authorization' => 'Bearer ' . $token
        ];

        //$this->assertAuthenticatedAs($loginData);
        $response = $this->withHeaders($headers)->postJson('/api/logout');


        $response->assertStatus(200);

        //$this->assertGuest();
    }
}
