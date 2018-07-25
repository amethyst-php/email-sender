<?php

namespace Railken\LaraOre\EmailSender\Exceptions;

class EmailSenderRenderException extends EmailSenderException
{
    /**
     * The code to identify the error.
     *
     * @var string
     */
    protected $code = 'EMAIL-SENDER_RENDER_ERROR';

    /**
     * Construct.
     *
     * @param mixed $message
     */
    public function __construct($message = null)
    {
        $this->message = $message;

        parent::__construct();
    }
}
