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
        $bag->set('name', $faker->name);
        $bag->set('description', $faker->text);
        $bag->set('type', 'MorphOne');
        $bag->set('source', 'foo');
        $bag->set('target', 'foo');
        $bag->set('filter', $faker->name);

        return $bag;
    }
}
