<?php

declare(strict_types=1);

namespace App\Services\SmsSender;

use App\Models\PlaneReservation;
use App\Services\SmsSender\SmsException;

class SmsService
{
    public function __construct(
        private SmsSender $smsSender,
        private readonly string $smsTitleSenderName,
        private readonly string $smsFooterSenderName,
    ) {
    }

    /**
     * @throws SmsException
     */
    public function sendReservationConfirmation(PlaneReservation $reservation): void
    {
        $sendSms = function (string $toPhone) use ($reservation): void {
            $this->smsSender->sendSms(
                $this->smsTitleSenderName,
                $toPhone,
                sprintf(
                    <<<'END'
Rezerwacja samolotu %s na dzień %s %s-%s DMT została potwierdzona.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
%s
END,
                    $reservation->plane->registration,
                    $reservation->starts_at->format('Y-m-d'),
                    $reservation->starts_at->timezone('Europe/Warsaw')->format('H:i'),
                    $reservation->ends_at->timezone('Europe/Warsaw')->format('H:i'),
                    $this->smsFooterSenderName
                )
            );
        };

        $sendSms($reservation->user->phone);

        if ($reservation->user2 !== null) {
            $sendSms($reservation->user2->phone);
        }
    }

    /**
     * @throws SmsException
     */
    public function sendReservationCancellation(PlaneReservation $reservation): void
    {
        $sendSms = function (string $toPhone) use ($reservation): void {
            $this->smsSender->sendSms(
                $this->smsTitleSenderName,
                $toPhone,
                sprintf(
                    <<<'END'
Rezerwacja samolotu %s na dzień %s %s-%s DMT została usunięta.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
%s
END,
                    $reservation->plane->registration,
                    $reservation->starts_at->format('Y-m-d'),
                    $reservation->starts_at->timezone('Europe/Warsaw')->format('H:i'),
                    $reservation->ends_at->timezone('Europe/Warsaw')->format('H:i'),
                    $this->smsFooterSenderName
                )
            );
        };

        $sendSms($reservation->user->phone);

        if ($reservation->user2 !== null) {
            $sendSms($reservation->user2->phone);
        }
    }
}
