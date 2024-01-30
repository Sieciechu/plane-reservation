<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\SmsSender;

use Tests\TestCase;

/**
 * @group paid
 */
class SmsPlanetClientTest extends TestCase
{
    public function testSendSms(): void
    {
        /** @var \App\Infrastructure\SmsSender\SmsPlanetClient $smsSender*/
        $smsSender = $this->app->get('epomSmsSender');

        $smsSender->sendSms(
            getenv('SMSCLIENT_TEST_PHONE_NUMBER'),
            <<<'END'
Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT(!) została potwierdzona.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Aeroklub Ostrowski
END
        );
    }

    public function testVendorClient(): void
    {
        $this->markTestSkipped();
        $smsClient = new \SMSPLANET\PHP\Client([
            'key' => getenv('SMSPLANET_API_APITOKEN'),
            'password' => getenv('SMSPLANET_API_APIPASSWORD'),
        ]);

        $message_id = $smsClient->sendSimpleSMS([
            'from' => 'A.Ostrowski',
            'to' => getenv('SMSCLIENT_TEST_PHONE_NUMBER'),
            'msg' => <<<'END'
[Vendor]Rezerwacja samolotu SP-APP na dzień 2024-01-05 14:40-15:40 DMT(!) została potwierdzona.
W razie wątpliwości prosimy o kontakt z instruktorem lub szefem wyszkolenia.
Pozdrawiamy,
Aeroklub Ostrowski
END,
        ]);
        echo "Message ID: $message_id\n";
        $this->assertTrue(true);
    }
}
