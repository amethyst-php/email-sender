<?php

namespace Railken\LaraOre\EmailSender\Exceptions;

class EmailSenderNotAuthorizedException extends EmailSenderException
{
    /**
     * The code to identify the error.
     *
     * @var string
     */
    protected $code = 'EMAILSENDER_NOT_AUTHORIZED';

    /**
     * The message.
     *
     * @var string
     */
    protected $message = "You're not authorized to interact with %s, missing %s permission";
}
