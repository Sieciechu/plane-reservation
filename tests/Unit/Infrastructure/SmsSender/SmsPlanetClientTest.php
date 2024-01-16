<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\SmsSender;

use App\Infrastructure\SmsSender\SmsPlanetClient;
use App\Services\SmsSender\SmsException;
use PHPUnit\Framework\MockObject\MockObject;
use SMSPLANET\PHP\Client;
use Tests\TestCase;

class SmsPlanetClientTest extends TestCase
{
    private SmsPlanetClient $client;
    private Client|MockObject $vendorMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->vendorMock = $this->createMock(Client::class);

        $this->client = new SmsPlanetClient($this->vendorMock);
    }

    public function testSendSmsThrowsExceptionWhenRecipientIsEmpty(): void
    {
        $this->expectException(SmsException::class);

        $this->client->sendSms('some sender', '', 'Test message');
    }

    public function testSendSmsThrowsExceptionWhenMessageIsEmpty(): void
    {
        $this->expectException(SmsException::class);

        $this->client->sendSms('some sender', '1234567890', '');
    }

    public function testSendSmsThrowsExceptionWhenSenderIsEmpty(): void
    {
        $this->expectException(SmsException::class);

        $this->client->sendSms('', '1234567890', 'Test message');
    }

    public function testSmsIsSent(): void
    {
        // assert
        $this->vendorMock->expects($this->once())
            ->method('sendSimpleSMS')
            ->with([
                'from' => 'some sender',
                'to' => '1234567890',
                'msg' => 'Test message',
            ]);

        // when
        $this->client->sendSms('some sender', '1234567890', 'Test message');
    }
}
