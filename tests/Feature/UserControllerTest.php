<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_get_the_users_returns_the_list(): void
    {
        // given
        \App\Models\User::factory(3)->create();

        // when
        $response = $this->get('/api/user/');

        // then
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $this->assertDatabaseCount('users', 3);
    }

    // test when I send post to register new user he should be created
    public function test_post_the_user_returns_the_user(): void
    {
        // when
        $response = $this->post('/api/user/', [
            'name' => 'John Doe',
            'email' => 'email@post.com',
            'password' => 'somepassword',
        ]);

        // then
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ]);

        // assert data
        $response->assertJson([
            'data' => [
                'name' => 'John Doe',
                'email' => 'email@post.com',
            ],
        ]);

        $this->assertDatabaseCount('users', 1);
    }
}
