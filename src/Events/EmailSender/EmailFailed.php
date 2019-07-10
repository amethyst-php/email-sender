<?php

namespace Amethyst\Events\EmailSender;

use Amethyst\Models\EmailSender;
use Exception;
use Illuminate\Queue\SerializesModels;
use Railken\Lem\Contracts\AgentContract;

class EmailFailed
{
    use SerializesModels;

    public $email;
    public $error;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Amethyst\Models\EmailSender         $email
     * @param \Exception                           $exception
     * @param \Railken\Lem\Contracts\AgentContract $agent
     */
    public function __construct(EmailSender $email, Exception $exception, AgentContract $agent = null)
    {
        $this->email = $email;
        $this->error = (object) [
            'class'   => get_class($exception),
            'message' => $exception->getMessage(),
        ];

        $this->agent = $agent;
    }
}
