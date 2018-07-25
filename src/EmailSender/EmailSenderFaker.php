<?php

namespace Railken\LaraOre\EmailSender;

use Faker\Factory;
use Railken\Bag;
use Railken\LaraOre\DataBuilder\DataBuilderFaker;
use Railken\Laravel\Manager\BaseFaker;

class EmailSenderFaker extends BaseFaker
{
    /**
     * @var string
     */
    protected $manager = EmailSenderManager::class;

    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('name', $faker->name);
        $bag->set('description', $faker->text);
        $bag->set('data_builder', DataBuilderFaker::make()->parameters()->toArray());
        $bag->set('subject', 'test');
        $bag->set('body', 'test');
        $bag->set('sender', 'test@test.net');
        $bag->set('recipients', 'test@test.net');
        $bag->set('attachments', [
            [
                'as'     => 'test.txt',
                'source' => 'file',
            ],
        ]);

        return $bag;
    }
}
