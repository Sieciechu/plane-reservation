<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\SmsSender;

use App\Infrastructure\SmsSender\DummySmsClient;
use App\Services\SmsSender\SmsException;
use Tests\TestCase;

class DummySmsClientTest extends TestCase
{
    public function testSendSmsThrowsExceptionWhenRecipientIsEmpty(): void
    {
        $this->expectException(SmsException::class);

        $dummySmsClient = new DummySmsClient();
        $dummySmsClient->sendSms('some sender name', '', 'Test message');
    }

    public function testSendSmsThrowsExceptionWhenMessageIsEmpty(): void
    {
        $this->expectException(SmsException::class);

        $dummySmsClient = new DummySmsClient();
        $dummySmsClient->sendSms('some sender name', '1234567890', '');
    }

    public function testSendSmsThrowsExceptionWhenSenderIsEmpty(): void
    {
        $this->expectException(SmsException::class);

        $dummySmsClient = new DummySmsClient();
        $dummySmsClient->sendSms('', '1234567890', 'Test message');
    }

    public function testSendSms(): void
    {
        $dummySmsClient = new DummySmsClient();
        $dummySmsClient->sendSms('some sender name', '1234567890', 'Test message');

        $this->assertCount(1, $dummySmsClient->smses);
        $this->assertEquals('some sender name', $dummySmsClient->smses[0]['from']);
        $this->assertEquals('1234567890', $dummySmsClient->smses[0]['to']);
        $this->assertEquals('Test message', $dummySmsClient->smses[0]['msg']);
    }
}
