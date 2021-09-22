<?php

return [
    'table_name' => 'mfa_otp',
    'database_connection' => env('MFA_DB_CONNECTION'),

    'passwords' => [
        'count' => 10,
        'length' => 10,
    ],
];
