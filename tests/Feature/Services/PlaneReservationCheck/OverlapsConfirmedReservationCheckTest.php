<?php

declare(strict_types=1);

namespace Tests\Feature\PlaneReservationCheck;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Models\UserRole;
use App\Services\PlaneReservationCheck\Exception;
use App\Services\PlaneReservationCheck\OverlapsConfirmedReservationCheck;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\Ulid;
use Tests\TestCase;

class OverlapsConfirmedReservationCheckTest extends TestCase
{
    use RefreshDatabase;

    private OverlapsConfirmedReservationCheck $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new OverlapsConfirmedReservationCheck();
    }
    /**
      * @test
      * @dataProvider overlapingReservationsProvider
      */
    public function whenOverlapsConfirmedReservationItShouldBeImpossibleToReserve(string $start, string $end): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M')]);
        Plane::factory()->create(['id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'confirmed_at' => '2021-01-01 09:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'confirmed_at' => '2021-01-01 09:00:00',
            'time' => 60,
        ]);
        // this is different plane, it should not count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-10 13:00:00',
            'ends_at' => '2021-01-10 13:30:00',
            'confirmed_at' => '2021-01-01 09:00:00',
            'time' => 60,
        ]);

        // assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('reservation overlaps with another confirmed reservation');
        
        // when
        $this->service->check(
            CarbonImmutable::parse($start),
            CarbonImmutable::parse($end),
            $this->createMock(User::class),
            '01HE1GB7NJSMF037F76BVR1D1M',
        );
    }

    public static function overlapingReservationsProvider(): iterable
    {
        yield ['2021-01-01 09:00', '2021-01-01 10:01'];
        yield ['2021-01-01 10:59', '2021-01-01 12:00'];
        yield ['2021-01-01 10:00', '2021-01-01 14:01'];
        yield ['2021-01-01 09:00', '2021-01-01 10:01'];
        yield ['2021-01-15 12:30', '2021-01-16 17:00'];
        yield ['2021-01-16 12:30', '2021-01-16 15:30'];
    }

    /**
     * @test
     * @dataProvider nonOverlapingReservationsProvider
     */
    public function whenReservationDoesNotOverlapConfirmedItShouldBePossibleToReserve(string $start, string $end): void
    {
        // given
        $user = new User([
            'id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'role' => UserRole::User
        ]);

        Plane::factory()->create(['id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M')]);
        Plane::factory()->create(['id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR')]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-01 10:00:00',
            'ends_at' => '2021-01-01 11:00:00',
            'time' => 60,
        ]);
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GB7NJSMF037F76BVR1D1M'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-16 15:00:00',
            'ends_at' => '2021-01-16 16:00:00',
            'time' => 60,
        ]);
        // this is different plane, it should not count
        PlaneReservation::factory()->create([
            'plane_id' => Ulid::fromString('01HE1GRM0B8RQTEX4KYFT7Q7TR'),
            'user_id' => Ulid::fromString('01HE1F50RYFHQS5HCTYWHDWYKY'),
            'starts_at' => '2021-01-10 13:00:00',
            'ends_at' => '2021-01-10 14:00:00',
            'time' => 60,
        ]);
        
        // when
        $this->service->check(
            CarbonImmutable::parse($start),
            CarbonImmutable::parse($end),
            $this->createMock(User::class),
            '01HE1GB7NJSMF037F76BVR1D1M',
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
        yield 'when overlaps unconfirmed it should be possible to reserve' => ['2021-01-10 10:20', '2021-01-10 14:59'];
    }
}
