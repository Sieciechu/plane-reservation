<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\SmsSender;

use App\Infrastructure\SmsSender\DummySmsClient;
use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;
use App\Services\SmsSender\SmsService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

/**
 * @group paid
 */
class SmsServiceTest extends TestCase
{
    private DummySmsClient $smsClient;
    private SmsService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->smsClient = new DummySmsClient($this->createMock(LoggerInterface::class));
        $this->service = new SmsService(
            $this->smsClient,
            'Samoloty AO',
            'Aeroklub Ostrowski',
        );
    }
    public function testSendReservationConfirmation(): void
    {
        // given
        $reservation = new PlaneReservation();
        $reservation->plane_id = 1;
        $reservation->plane = new Plane();
        $reservation->plane->registration = 'SP-APP';
        $reservation->starts_at = CarbonImmutable::parse('2024-01-05 14:40:00');
        $reservation->ends_at = CarbonImmutable::parse('2024-01-05 15:40:00');
        $reservation->user = new User();
        $reservation->user->phone = '123456789';
        $reservation->user2 = new User();
        $reservation->user2->phone = '987654321';

        // when
        $this->service->sendReservationConfirmation($reservation);

        // then
        $this->assertCount(2, $this->smsClient->smses);
        $this->assertEquals('Samoloty  AO', $this->smsClient->smses[0]['from']);
        $this->assertEquals('123456789', $this->smsClient->smses[0]['to']);
        $this->assertEquals(
            <<<'END'
Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT została potwierdzona.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Samoloty  AO
END,
            $this->smsClient->smses[0]['msg']
        );
        
        $this->assertEquals('Samoloty  AO', $this->smsClient->smses[1]['from']);
        $this->assertEquals('987654321', $this->smsClient->smses[1]['to']);
        $this->assertEquals(
            <<<'END'
Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT została potwierdzona.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Samoloty  AO
END,
            $this->smsClient->smses[1]['msg']
        );
    }

    public function testSendReservationCancellation(): void
    {
        // given
        $reservation = new PlaneReservation();
        $reservation->plane_id = 1;
        $reservation->plane = new Plane();
        $reservation->plane->registration = 'SP-APP';
        $reservation->starts_at = CarbonImmutable::parse('2024-01-05 14:40:00');
        $reservation->ends_at = CarbonImmutable::parse('2024-01-05 15:40:00');
        $reservation->user = new User();
        $reservation->user->phone = '123456789';
        $reservation->user2 = new User();
        $reservation->user2->phone = '987654321';

        // when
        $this->service->sendReservationCancellation($reservation);

        // then
        $this->assertCount(2, $this->smsClient->smses);
        $this->assertEquals('Samoloty  AO', $this->smsClient->smses[0]['from']);
        $this->assertEquals('123456789', $this->smsClient->smses[0]['to']);
        $this->assertEquals(
            <<<'END'
Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT została usunięta.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Aeroklub Ostrowski
END,
            $this->smsClient->smses[0]['msg']
        );
        
        $this->assertEquals('Samoloty  AO', $this->smsClient->smses[1]['from']);
        $this->assertEquals('987654321', $this->smsClient->smses[1]['to']);
        $this->assertEquals(
            <<<'END'
Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT została usunięta.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Aeroklub Ostrowski
END,
            $this->smsClient->smses[1]['msg']
        );
    }
}
