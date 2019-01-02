<?php

namespace Railken\Amethyst\Tests\Managers;

use Railken\Amethyst\Fakers\EmailSenderFaker;
use Railken\Amethyst\Managers\DataBuilderManager;
use Railken\Amethyst\Managers\EmailSenderManager;
use Railken\Amethyst\Tests\BaseTest;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class EmailSenderTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = EmailSenderManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = EmailSenderFaker::class;

    public function testSend()
    {
        $manager = $this->getManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();

        $result = $manager->send($resource, ['name' => $resource->name, 'file' => 'https://assets-cdn.github.com/images/modules/logos_page/GitHub-Mark.png']);
        $this->assertEquals(true, $result->ok());
    }

    public function testRender()
    {
        $manager = $this->getManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());

        $resource = $result->getResource();

        $result = $manager->render($resource->data_builder, [
            'body' => '{{ name }}',
        ], (new DataBuilderManager())->build($resource->data_builder, ['name' => 'ban'])->getResource());

        $this->assertEquals(true, $result->ok());
        $this->assertEquals('ban', $result->getResource()['body']);
    }
}
