<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseTruncation;
    
    public function test_get_the_users_returns_the_list(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        User::factory(3)->create();

        // when
        $response = $this->get('/api/user/');

        // then
        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data');
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

        $this->assertDatabaseCount('users', 4);
    }

    public function test_it_should_be_possible_to_register_the_user(): void
    {
        // when
        $response = $this->post('/api/user/', [
            'name' => 'John Doe',
            'email' => 'email@post.com',
            'phone' => '0048123456789',
            'password' => 'somepassword',
            'password_confirmation' => 'somepassword',
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

    public function test_it_should_be_possible_to_login(): void
    {
        // given

        // user is created
        $this->post('/api/user/', [
            'name' => 'John Doe',
            'email' => 'some.email@post.com',
            'phone' => '0048123456789',
            'password' => 'somepassword',
            'password_confirmation' => 'somepassword',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);

        // when
        $response = $this->post('/api/user/login', [
            'email' => 'some.email@post.com',
            'password' => 'somepassword',
        ]);

        // then
        $response->assertStatus(200);
        $response->assertJsonStructure(['auth_token']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /**
     * @dataProvider credentialsProvider
     */
    public function test_when_login_email_is_invalid_it_should_be_impossible_to_login(string $email, string $password): void
    {
        // given

        // user is created
        $this->post('/api/user/', [
            'name' => 'John Doe',
            'email' => 'some.email@post.com',
            'password' => 'somepassword',
            'password_confirmation' => 'somepassword',
        ]);

        // when
        $response = $this->post('/api/user/login', [
            'email' => $email,
            'password' => $password,
        ]);

        // then
        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Invalid login or password',
        ]);
        $response->assertJsonMissing(['auth_token']);
    }

    public static function credentialsProvider(): iterable
    {
        yield 'invalid email' => [
            'email' => 'someTypo@email.com',
            'password' => 'somepassword',
        ];
        yield 'invalid password' => [
            'email' => 'some.email@post.com',
            'password' => 'some-wrong-password',
        ];
    }
}
