<?php

declare(strict_types=1);

namespace App\Infrastructure\SmsSender;

use App\Services\SmsSender\SmsException;
use App\Services\SmsSender\SmsSender;
use Psr\Log\LoggerInterface;

class DummySmsClient implements SmsSender
{
    public array $smses = [];
    public function __construct(
        private LoggerInterface $logger,
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

        $this->smses[] = [
            'from' => $from,
            'to' => $to,
            'msg' => $msg,
        ];

        $this->logger->info("DummySmsClient: SMS sent", [
            'from' => $from,
            'to' => $to,
            'msg' => $msg,
        ]);
    }
}
