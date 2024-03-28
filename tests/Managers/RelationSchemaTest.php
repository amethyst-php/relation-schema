<?php

namespace Amethyst\Tests\Managers;

use Amethyst\Fakers\RelationSchemaFaker;
use Amethyst\Managers\RelationSchemaManager;
use Amethyst\Tests\Base;
use Railken\Lem\Support\Testing\TestableBaseTrait;

class RelationSchemaTest extends Base
{
    use TestableBaseTrait;

    /**
     * Manager class.
     *
     * @var string
     */
    protected $manager = RelationSchemaManager::class;

    /**
     * Faker class.
     *
     * @var string
     */
    protected $faker = RelationSchemaFaker::class;
}
