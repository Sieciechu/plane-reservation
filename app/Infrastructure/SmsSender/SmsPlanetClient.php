<?php

declare(strict_types=1);

namespace App\Infrastructure\SmsSender;

use App\Services\SmsSender\SmsException;
use App\Services\SmsSender\SmsSender;
use SMSPLANET\PHP\Client;

class SmsPlanetClient implements SmsSender
{
    public function __construct(
        private Client $client,
    ) {
    }

    /**
     * @throws SmsException
     */
    public function sendSms(string $from, string $to, string $msg): void
    {
        if ("" === $from) {
            throw new SmsException("From cannot be empty");
        }

        if ("" === $to) {
            throw new SmsException("To phone number cannot be empty");
        }

        if ("" === $msg) {
            throw new SmsException("Message cannot be empty");
        }

        try {
            $this->client->sendSimpleSMS([
                'from' => $from,
                'to' => $to,
                'msg' => $msg,
            ]);
        } catch (\Throwable $e) {
            throw new SmsException("Error while sending SMS: " . $e->getMessage());
        }
    }
}
