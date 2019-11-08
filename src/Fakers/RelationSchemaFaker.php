<?php

namespace Amethyst\Fakers;

use Faker\Factory;
use Railken\Bag;
use Symfony\Component\Yaml\Yaml;
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
        $bag->set('type', 'MorphToOne');
        $bag->set('data', 'foo');
        $bag->set('payload', Yaml::dump(['target' => 'foo']));

        return $bag;
    }
}
