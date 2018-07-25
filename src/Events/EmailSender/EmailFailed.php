<?php

namespace Railken\LaraOre\Events\EmailSender;

use Exception;
use Illuminate\Queue\SerializesModels;
use Railken\LaraOre\EmailSender\EmailSender;
use Railken\Laravel\Manager\Contracts\AgentContract;

class EmailFailed
{
    use SerializesModels;

    public $email;
    public $error;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Railken\LaraOre\EmailSender\EmailSender         $email
     * @param \Exception                                       $exception
     * @param \Railken\Laravel\Manager\Contracts\AgentContract $agent
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
