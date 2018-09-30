<?php

namespace Railken\Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class EmailSenderAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'email-sender.create',
        Tokens::PERMISSION_UPDATE => 'email-sender.update',
        Tokens::PERMISSION_SHOW   => 'email-sender.show',
        Tokens::PERMISSION_REMOVE => 'email-sender.remove',
    ];
}
