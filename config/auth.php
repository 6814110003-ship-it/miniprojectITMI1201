<?php

use App\Models\Employee;
use App\Models\Member;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'members'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'members',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'employees',
        ],
    ],

    'providers' => [
        'members' => [
            'driver' => 'eloquent',
            'model' => Member::class,
        ],

        'employees' => [
            'driver' => 'eloquent',
            'model' => Employee::class,
        ],
    ],

    'passwords' => [
        'members' => [
            'provider' => 'members',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];