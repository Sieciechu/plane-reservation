<?php

declare(strict_types=1);

namespace App\Services\SmsSender;

interface SmsSender
{
    /**
     * @param string $from Name or number of the sender
     * @param string $to Phone number of the recipient
     * @param string $message Message to be sent
     *
     * @throws SmsException
     */
    public function sendSms(string $from, string $to, string $message): void;
}
