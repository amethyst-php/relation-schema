<?php

namespace Amethyst\Fakers;

use Faker\Factory;
use Railken\Bag;
use Railken\Lem\Faker;

class RelationSchemaFaker extends Faker
{
    /**
     * @return \Railken\Bag
     */
    public function parameters()
    {
        $faker = Factory::create();

        $bag = new Bag();
        $bag->set('name', 'rel');
        $bag->set('description', $faker->text);
        $bag->set('type', 'MorphTo');
        $bag->set('data', 'bar');

        return $bag;
    }
}
