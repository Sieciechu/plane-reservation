<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\SmsSender;

use Tests\TestCase;

/**
 * @group paid
 */
class SmsPlanetClientTest extends TestCase
{
    public function testVendorClient(): void
    {
        $smsClient = new \SMSPLANET\PHP\Client([
            'key' => getenv('SMSPLANET_API_APITOKEN'),
            'password' => getenv('SMSPLANET_API_APIPASSWORD'),
        ]);

        $message_id = $smsClient->sendSimpleSMS([
            'from' => 'Samoloty AO',
            'to' => getenv('SMSCLIENT_TEST_PHONE_NUMBER'),
            'msg' => <<<'END'
[Test][Vendor]Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT(!) została potwierdzona.
END,
        ]);
        echo "Message ID: $message_id\n";
        $this->assertTrue(true);
    }
}
