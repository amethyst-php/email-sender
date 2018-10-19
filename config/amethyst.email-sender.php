<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    |
    | Here you can change the table name and the class components.
    |
    */
    'data' => [
        'email-sender' => [
            'table'      => 'amethyst_email_senders',
            'comment'    => 'Email Sender',
            'model'      => Railken\Amethyst\Models\EmailSender::class,
            'schema'     => Railken\Amethyst\Schemas\EmailSenderSchema::class,
            'repository' => Railken\Amethyst\Repositories\EmailSenderRepository::class,
            'serializer' => Railken\Amethyst\Serializers\EmailSenderSerializer::class,
            'validator'  => Railken\Amethyst\Validators\EmailSenderValidator::class,
            'authorizer' => Railken\Amethyst\Authorizers\EmailSenderAuthorizer::class,
            'faker'      => Railken\Amethyst\Fakers\EmailSenderFaker::class,
            'manager'    => Railken\Amethyst\Managers\EmailSenderManager::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Http configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the routes
    |
    */
    'http' => [
        'admin' => [
            'email-sender' => [
                'enabled'     => true,
                'controller'  => Railken\Amethyst\Http\Controllers\Admin\EmailSendersController::class,
                'router'      => [
                    'as'        => 'email-sender.',
                    'prefix'    => '/email-senders',
                ],
            ],
        ],
    ],
];
