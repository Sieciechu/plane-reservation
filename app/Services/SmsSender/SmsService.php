<?php

declare(strict_types=1);

namespace App\Services\SmsSender;

use App\Models\Plane;
use App\Models\PlaneReservation;
use App\Models\User;

class SmsService
{
    public function __construct(
        private SmsSender $smsSender,
        private readonly string $smsSenderName,
    ) {
    }

    public function sendReservationConfirmation(PlaneReservation $reservation): void
    {
        /** @var Plane $plane */
        $plane = Plane::where('id', $reservation->plane_id)->firstOrFail();

        $sendSms = function (string $toPhone) use ($reservation, $plane): void {
            $this->smsSender->sendSms(
                $this->smsSenderName,
                $toPhone,
                sprintf(
                    <<<'END'
    Rezerwacja samolotu %s na dzień %s %s-%s DMT została potwierdzona.
    W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
    Pozdrawiamy,
    Aeroklub Ostrowski
    END,
                    $plane->registration,
                    $reservation->starts_at->format('Y-m-d'),
                    $reservation->starts_at->timezone('Europe/Warsaw')->format('H:i'),
                    $reservation->ends_at->timezone('Europe/Warsaw')->format('H:i')
                )
            );
        };

        $sendSms($reservation->user->phone);

        if ($reservation->user2 !== null) {
            $sendSms($reservation->user2->phone);
        }
    }

    public function sendReservationCancellation(PlaneReservation $reservation): void
    {
        /** @var Plane $plane */
        $plane = Plane::where('id', $reservation->plane_id)->firstOrFail();
        /** @var User $reservationUser */
        $reservationUser = User::where('id', $reservation->user_id)->firstOrFail();

        $this->smsSender->sendSms(
            $this->smsSenderName,
            $reservationUser->phone,
            sprintf(
                <<<'END'
Rezerwacja samolotu %s na dzień %s %s-%s DMT została usunięta.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Aeroklub Ostrowski
END,
                $plane->registration,
                $reservation->starts_at->format('Y-m-d'),
                $reservation->starts_at->timezone('Europe/Warsaw')->format('H:i'),
                $reservation->ends_at->timezone('Europe/Warsaw')->format('H:i')
            )
        );
    }
}
