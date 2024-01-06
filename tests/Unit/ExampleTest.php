<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function test_sms(): void
    {
        $this->markTestSkipped();
        return;
        $smsClient = new \SMSPLANET\PHP\Client([
            'key' => '33e9bf30-314e-490b-8e18-186f27909db4',
            'password' => '9dVQUeQ2QfLNb94oMDPjvMLZzsSUTFWDHhxQ54RcvXDQ--nQr5FVgWRP-joukxAX',
        ]);

        $message_id = $smsClient->sendSimpleSMS([
            'from' => 'A.Ostrowski',             // Nazwa nadawcy zgodnie z ustawieniami konta
            'to' => '0048603866178',
            'msg' => "Treść wiadomości test 0048",
// Jakaś nowa linia.
// Cokolwiek. W razie wątpliwości skontaktuj się z instruktorem pod nr +48603866178 lub szefem wyszkolenia nr +48123456789

// Lub zadzwoń pod nr: +48603866178.

// Pozdrawiamy,
// Aeroklub Ostrowski"
        ]);
        var_dump($message_id);
        $this->assertTrue(true);
    }
}
