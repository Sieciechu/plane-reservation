<?php

namespace Tests\Feature;

use App\Models\Plane;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlaneControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_get_the_planes_returns_the_list(): void
    {
        // given
        \App\Models\Plane::factory(3)->create();
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        // when
        $response = $this->get('/api/plane/');

        // then
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'registration',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);
    }

    public function test_get_the_specific_plane_returns_it(): void
    {
        // given
        Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        // when
        $response = $this->get('/api/plane/SP-KYS');

        // then
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'registration',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ]);
        $response->assertJson([
            'data' => [
                'name' => 'PZL Koliber 150',
                'registration' => 'SP-KYS',
            ],
        ]);
    }

    public function test_get_the_specific_plane_returns_404_when_not_found(): void
    {
        // given
        Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ]);
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);

        // when
        $response = $this->get('/api/plane/SP-KYS2');

        // then
        $response->assertStatus(404);
    }

    public function test_post_the_plane_creates_it(): void
    {
        // given
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);
        Sanctum::actingAs($user, ['*']);
        
        $data = [
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-KYS',
        ];

        // when
        $response = $this->post('/api/plane/', $data);

        // then
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'registration',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ]);
        $response->assertJson([
            'data' => $data,
        ]);
        $this->assertDatabaseHas('planes', $data);
    }
}
