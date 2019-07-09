<?php

namespace Amethyst\Events\EmailSender;

use Illuminate\Queue\SerializesModels;
use Amethyst\Models\EmailSender;
use Railken\Lem\Contracts\AgentContract;

class EmailSent
{
    use SerializesModels;

    public $email;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @param \Amethyst\Models\EmailSender $email
     * @param \Railken\Lem\Contracts\AgentContract $agent
     */
    public function __construct(EmailSender $email, AgentContract $agent = null)
    {
        $this->email = $email;
        $this->agent = $agent;
    }
}
