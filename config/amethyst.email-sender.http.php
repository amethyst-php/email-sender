<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Http configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the routes
    |
    */
    'app' => [
        'email-sender' => [
            'enabled'    => true,
            'controller' => Amethyst\Http\Controllers\EmailSenderController::class,
            'router'     => [
                'prefix' => '/data/email-sender',
                'as'     => 'email-sender.',
            ],
        ],
    ],
];
