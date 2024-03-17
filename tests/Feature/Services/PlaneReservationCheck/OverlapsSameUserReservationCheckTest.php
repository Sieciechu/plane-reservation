<?php

declare(strict_types=1);

namespace Tests\Feature\Services\PlaneReservationCheck;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationCheck\Exception;
use App\Services\PlaneReservationCheck\OverlapsSameUserReservationCheck;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverlapsSameUserReservationCheckTest extends TestCase
{
    use RefreshDatabase;

    private OverlapsSameUserReservationCheck $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new OverlapsSameUserReservationCheck();
    }
    /**
      * @test
      * @dataProvider overlapingReservationsProvider
      */
    public function whenOverlapsSameUserReservationItShouldBeImpossibleToReserve(string $start, string $end): void
    {
        // given
        $user = new User([
            'id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '34efffb9-43a8-454f-8583-be59956991c7']);
        PlaneReservation::factory()->create([
            'plane_id' => '34efffb9-43a8-454f-8583-be59956991c7',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'confirmed_at' => null,
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => '34efffb9-43a8-454f-8583-be59956991c7',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 12:00:00',
            'ends_at' => '2021-01-01 13:00:00',
            'confirmed_at' => null,
            'time' => 60,
        ]);

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation cannot overlap with your other reservations');
        
        // when
        $this->service->check(
            CarbonImmutable::parse($start),
            CarbonImmutable::parse($end),
            $user,
            '34efffb9-43a8-454f-8583-be59956991c7',
        );
    }

    public static function overlapingReservationsProvider(): iterable
    {
        yield ['2021-01-01 09:00', '2021-01-01 10:01'];
        yield ['2021-01-01 10:15', '2021-01-01 11:20'];
        yield ['2021-01-01 09:15', '2021-01-01 11:20'];
        yield ['2021-01-01 09:15', '2021-01-01 10:20'];
        yield ['2021-01-01 10:59', '2021-01-01 12:00'];
        yield ['2021-01-01 10:00', '2021-01-01 14:01'];
        yield ['2021-01-01 09:00', '2021-01-01 11:45'];
    }

    /**
     * @test
     * @dataProvider nonOverlapingReservationsProvider
     */
    public function whenReservationDoesNotOverlapItShouldBePossibleToReserve(string $start, string $end): void
    {
        // given
        $user = new User([
            'id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => '34efffb9-43a8-454f-8583-be59956991c7']);
        Plane::factory()->create(['id' => '03bac3ab-175c-4f39-92de-29f4af4370f6']);
        PlaneReservation::factory()->create([
            'plane_id' => '34efffb9-43a8-454f-8583-be59956991c7',
            'user_id' => '30a6a6bf-6669-4c84-96d7-3f824d45b74b',
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'confirmed_at' => null,
            'time' => 60,
        ]);
        
        // when
        $this->service->check(
            CarbonImmutable::parse($start),
            CarbonImmutable::parse($end),
            $user,
            '34efffb9-43a8-454f-8583-be59956991c7',
        );
        $this->assertTrue(true);
    }

    public static function nonOverlapingReservationsProvider(): iterable
    {
        yield 'does not overlap any #1' => ['2021-01-01 09:00', '2021-01-01 10:00'];
        yield 'does not overlap any #2' => ['2021-01-01 11:00', '2021-01-01 12:00'];
        yield 'does not overlap any #3' => ['2021-01-10 13:00', '2021-01-10 14:00'];
        yield 'does not overlap any #4' => ['2021-01-10 09:00', '2021-01-10 09:59'];
        yield 'does not overlap any #5' => ['2021-01-10 11:40', '2021-01-10 14:59'];
    }
}
