<?php

namespace Amethyst\Fakers;

use Faker\Factory;
use Amethyst\DataBuilders\EmailSenderDataBuilder;
use Railken\Bag;
use Railken\Lem\Faker;
use Symfony\Component\Yaml\Yaml;

class EmailSenderFaker extends Faker
{
    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('name', $faker->name);
        $bag->set('description', $faker->text);
        $bag->set('data_builder', DataBuilderFaker::make()->parameters()->set('data_builder.class_name', EmailSenderDataBuilder::class)->toArray());
        $bag->set('subject', 'test');
        $bag->set('body', 'test');
        $bag->set('sender', 'test@test.net');
        $bag->set('recipients', 'test@test.net');
        $bag->set('attachments', Yaml::dump([
            [
                'as'     => '{{ name }}',
                'source' => '{{ file }}',
            ],
        ]));

        return $bag;
    }
}
