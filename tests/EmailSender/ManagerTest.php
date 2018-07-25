<?php

namespace Railken\LaraOre\Tests\EmailSender;

use Railken\LaraOre\EmailSender\EmailSenderFaker;
use Railken\LaraOre\EmailSender\EmailSenderManager;
use Railken\LaraOre\Support\Testing\ManagerTestableTrait;

class ManagerTest extends BaseTest
{
    use ManagerTestableTrait;

    /**
     * Retrieve basic url.
     *
     * @return \Railken\Laravel\Manager\Contracts\ManagerContract
     */
    public function getManager()
    {
        return new EmailSenderManager();
    }

    public function testSuccessCommon()
    {
        $this->commonTest($this->getManager(), EmailSenderFaker::make()->parameters());
    }
}
