<?php

return [
    'monthlyTimeLimitInMinutes' => 1200, // equals 20h
    'dailyTimeLimitInMinutes' => 240,
    'maxReservationDaysAhead' => 30,
    'airport' => [
        'EPOM' => [
            'latitude' => 51.70154,
            'longitude' => 17.84688,
            'timezone' => 'UTC',
            'sms' => [
                'titleFrom' => 'Samoloty AO',
                'footerFrom' => 'Aeroklub Ostrowski',
            ]
        ]
    ],
    'smsplanet' => [
        'apitoken' => getenv('SMSPLANET_API_APITOKEN'),
        'apipassword' => getenv('SMSPLANET_API_APIPASSWORD'),
    ]
];
