<?php //config/auth.php

return [
    'defaults' => [
        'guard' => 'staff_api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
        'staff_api' => [
            'driver' => 'jwt',
            'provider' => 'staff',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ],
        'staff' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Staff::class
        ]
    ]
];
