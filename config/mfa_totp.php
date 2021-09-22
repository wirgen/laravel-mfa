<?php

return [
    'table_name' => 'mfa_totp',
    'database_connection' => env('MFA_DB_CONNECTION'),

    'secret_length' => 32,
    'key' => [
        'algorithm' => 'sha1',
        'length' => 6,
        'period' => 30,
    ],
];
