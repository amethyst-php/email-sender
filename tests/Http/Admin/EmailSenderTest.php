<?php

namespace Amethyst\Tests\Http\Admin;

use Amethyst\Core\Support\Testing\TestableBaseTrait;
use Amethyst\Fakers\EmailSenderFaker;
use Amethyst\Managers\EmailSenderManager;
use Amethyst\Tests\BaseTest;

class EmailSenderTest extends BaseTest
{
    use TestableBaseTrait;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = EmailSenderFaker::class;

    /**
     * Router group resource.
     *
     * @var string
     */
    protected $group = 'admin';

    /**
     * Route name.
     *
     * @var string
     */
    protected $route = 'admin.email-sender';

    public function testSend()
    {
        $manager = new EmailSenderManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();

        $response = $this->callAndTest('POST', route('admin.email-sender.execute', ['id' => $resource->id]), ['data' => ['name' => $resource->name]], 200);
    }

    public function testRender()
    {
        $manager = new EmailSenderManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters());
        $this->assertEquals(1, $result->ok());

        $resource = $result->getResource();

        $response = $this->callAndTest('post', route('admin.email-sender.render'), [
            'data_builder_id' => $resource->data_builder->id,
            'body'            => '{{ name }}',
            'subject'         => 'Subject',
            'recipients'      => 'test@test.net',
            'data'            => ['name' => 'ban'],
        ], 200);

        $this->assertEquals('ban', base64_decode(json_decode($response->getContent())->resource->body, true));
    }
}
