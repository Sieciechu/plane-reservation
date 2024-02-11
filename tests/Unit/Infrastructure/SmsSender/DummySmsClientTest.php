<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\SmsSender;

use App\Infrastructure\SmsSender\DummySmsClient;
use App\Services\SmsSender\SmsException;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class DummySmsClientTest extends TestCase
{
    private DummySmsClient $dummySmsClient;

    private LoggerInterface|MockObject $logger;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->dummySmsClient = new DummySmsClient($this->logger);
    }
    /**
    * @dataProvider provideInvalidSmsData
    */
    public function testSendSmsThrowsExceptionWhenDataIsInvalid($sender, $recipient, $message): void
    {
        $this->expectException(SmsException::class);

        $this->dummySmsClient->sendSms($sender, $recipient, $message);
    }

    public static function provideInvalidSmsData(): array
    {
        return [
            'Recipient is empty' => ['some sender name', '', 'Test message'],
            'Message is empty' => ['some sender name', '1234567890', ''],
            'Sender is empty' => ['', '1234567890', 'Test message'],
        ];
    }

    public function testSendSms(): void
    {
        // assert
        $this->logger->expects($this->once())
            ->method('info')
            ->with('DummySmsClient: SMS sent', [
                'from' => 'some sender name',
                'to' => '1234567890',
                'msg' => 'Test message',
            ]);
        
        // when
        $this->dummySmsClient->sendSms('some sender name', '1234567890', 'Test message');

        // then
        $this->assertCount(1, $this->dummySmsClient->smses);
        $this->assertEquals('some sender name', $this->dummySmsClient->smses[0]['from']);
        $this->assertEquals('1234567890', $this->dummySmsClient->smses[0]['to']);
        $this->assertEquals('Test message', $this->dummySmsClient->smses[0]['msg']);
    }
}
