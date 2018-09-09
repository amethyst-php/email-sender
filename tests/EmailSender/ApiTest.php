<?php

namespace Railken\LaraOre\Tests\EmailSender;

use Illuminate\Support\Facades\Config;
use Railken\LaraOre\Api\Support\Testing\TestableBaseTrait;
use Railken\LaraOre\EmailSender\EmailSenderFaker;
use Railken\LaraOre\EmailSender\EmailSenderManager;

class ApiTest extends BaseTest
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
     * Base path config.
     *
     * @var string
     */
    protected $config = 'ore.email-sender';

    public function testSend()
    {
        $manager = new EmailSenderManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters()->set('data_builder.repository.class_name', \Railken\LaraOre\Tests\EmailSender\Repositories\EmailSenderRepository::class));
        $this->assertEquals(1, $result->ok());
        $resource = $result->getResource();

        $response = $this->callAndTest('POST', $this->getResourceUrl().'/'.$resource->id.'/send', ['data' => ['name' => $resource->name]], 200);
    }

    public function testRender()
    {
        $manager = new EmailSenderManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters()->set('data_builder.repository.class_name', \Railken\LaraOre\Tests\EmailSender\Repositories\EmailSenderRepository::class));
        $this->assertEquals(1, $result->ok());

        $resource = $result->getResource();

        $response = $this->callAndTest('post', $this->getResourceUrl().'/render', [
            'data_builder_id' => $resource->data_builder->id,
            'body'            => '{{ name }}',
            'subject'         => 'Subject',
            'recipients'      => 'test@test.net',
            'data'            => ['name' => 'ban'],
        ], 200);

        $this->assertEquals('ban', base64_decode(json_decode($response->getContent())->resource->body));
    }
}
