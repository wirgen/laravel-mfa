<?php

use Wirgen\LaravelMfa\Providers;

return [
    'table_name' => 'user_mfa',
    'database_connection' => env('MFA_DB_CONNECTION'),

    'types' => [
        'otp' => Providers\OtpProvider::class,
        'totp' => Providers\TotpProvider::class,
    ],
];
