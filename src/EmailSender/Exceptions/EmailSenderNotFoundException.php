<?php

namespace Railken\LaraOre\EmailSender\Exceptions;

class EmailSenderNotFoundException extends EmailSenderException
{
    /**
     * The code to identify the error.
     *
     * @var string
     */
    protected $code = 'EMAILSENDER_NOT_FOUND';

    /**
     * The message.
     *
     * @var string
     */
    protected $message = 'Not found';
}
