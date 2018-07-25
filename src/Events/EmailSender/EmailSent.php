<?php

namespace Railken\LaraOre\Events\EmailSender;

use Illuminate\Queue\SerializesModels;
use Railken\LaraOre\EmailSender\EmailSender;
use Railken\Laravel\Manager\Contracts\AgentContract;

class EmailSent
{
    use SerializesModels;

    public $email;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Railken\LaraOre\EmailSender\EmailSender         $email
     * @param \Railken\Laravel\Manager\Contracts\AgentContract $agent
     */
    public function __construct(EmailSender $email, AgentContract $agent = null)
    {
        $this->email = $email;
        $this->agent = $agent;
    }
}
