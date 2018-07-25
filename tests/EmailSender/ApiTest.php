<?php

namespace Railken\LaraOre\Tests\EmailSender;

use Illuminate\Support\Facades\Config;
use Railken\LaraOre\EmailSender\EmailSenderFaker;
use Railken\LaraOre\EmailSender\EmailSenderManager;
use Railken\LaraOre\Support\Testing\ApiTestableTrait;

class ApiTest extends BaseTest
{
    use ApiTestableTrait;

    /**
     * Retrieve basic url.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return Config::get('ore.api.router.prefix').Config::get('ore.email-sender.http.admin.router.prefix');
    }

    /**
     * Test common requests.
     */
    public function testSuccessCommon()
    {
        $this->commonTest($this->getBaseUrl(), EmailSenderFaker::make()->parameters());
    }

    public function testRender()
    {
        $manager = new EmailSenderManager();

        $result = $manager->create(EmailSenderFaker::make()->parameters()->set('data_builder.repository.class_name', \Railken\LaraOre\Tests\EmailSender\Repositories\EmailSenderRepository::class));
        $this->assertEquals(1, $result->ok());

        $resource = $result->getResource();

        $response = $this->post($this->getBaseUrl().'/render', [
            'data_builder_id' => $resource->data_builder->id,
            'body'            => '{{ name }}',
            'data'            => ['name' => 'ban'],
        ]);

        $response->assertStatus(200);
        $this->assertEquals('ban', base64_decode(json_decode($response->getContent())->resource));
    }
}
