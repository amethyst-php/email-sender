<?php

namespace Railken\LaraOre\Tests\EmailSender;

use Railken\LaraOre\EmailSender\EmailSenderFaker;
use Railken\LaraOre\EmailSender\EmailSenderManager;
use Railken\LaraOre\File\FileManager;
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

    public function testSend()
    {
        $manager = $this->getManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters()->set('data_builder.repository.class_name', \Railken\LaraOre\Tests\EmailSender\Repositories\EmailSenderRepository::class));
        $this->assertEquals(1, $result->ok());

        $resource = $result->getResource();
        $fm = new FileManager();
        $result = $fm->uploadFileByContent('hello my friend', 'welcome.txt');
        $file = $result->getResource();

        $result = $manager->send($resource, ['name' => $resource->name, 'file' => $file]);
        $this->assertEquals(true, $result->ok());
    }

    public function testRender()
    {
        $manager = $this->getManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters()->set('data_builder.repository.class_name', \Railken\LaraOre\Tests\EmailSender\Repositories\EmailSenderRepository::class));
        $this->assertEquals(1, $result->ok());

        $resource = $result->getResource();

        $result = $manager->render($resource->data_builder, '{{ name }}', ['name' => 'ban']);

        $this->assertEquals(true, $result->ok());
        $this->assertEquals('ban', $result->getResource());
    }
}
