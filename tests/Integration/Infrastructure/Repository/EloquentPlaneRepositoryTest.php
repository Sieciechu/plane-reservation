<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Infrastructure\Repository\EloquentPlaneRepository;
use App\Models\Plane;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentPlaneRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentPlaneRepository $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo = new EloquentPlaneRepository();
    }

    /** @test */
    public function itShouldReturnEmptyArrayWhenNoPlanes(): void
    {
        // when
        $result = $this->repo->getAll();

        // then
        $this->assertEmpty($result);
    }

    /** @test */
    public function itShouldReturnAllPlanesOrderedByRegistration(): void
    {
        // given
        Plane::factory()->create([
            'name' => 'PZL Koliber 150',
            'registration' => 'SP-ABC',
        ]);
        Plane::factory()->create([
            'name' => 'PZL Wilga 2000',
            'registration' => 'SP-DEF',
        ]);

        // when
        $result = $this->repo->getAll();

        // then
        $this->assertCount(2, $result);
        $this->assertEquals('SP-ABC', $result[0]->registration);
        $this->assertEquals('PZL Koliber 150', $result[0]->name);

        $this->assertEquals('SP-DEF', $result[1]->registration);
        $this->assertEquals('PZL Wilga 2000', $result[1]->name);
    }
}
