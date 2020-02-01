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
            'model'      => Amethyst\Models\EmailSender::class,
            'schema'     => Amethyst\Schemas\EmailSenderSchema::class,
            'repository' => Amethyst\Repositories\EmailSenderRepository::class,
            'serializer' => Amethyst\Serializers\EmailSenderSerializer::class,
            'validator'  => Amethyst\Validators\EmailSenderValidator::class,
            'authorizer' => Amethyst\Authorizers\EmailSenderAuthorizer::class,
            'faker'      => Amethyst\Fakers\EmailSenderFaker::class,
            'manager'    => Amethyst\Managers\EmailSenderManager::class,
        ],
    ],
];
