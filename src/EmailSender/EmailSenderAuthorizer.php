<?php

namespace Railken\LaraOre\EmailSender;

use Railken\Laravel\Manager\ModelAuthorizer;
use Railken\Laravel\Manager\Tokens;

class EmailSenderAuthorizer extends ModelAuthorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'email_sender.create',
        Tokens::PERMISSION_UPDATE => 'email_sender.update',
        Tokens::PERMISSION_SHOW   => 'email_sender.show',
        Tokens::PERMISSION_REMOVE => 'email_sender.remove',
    ];
}
