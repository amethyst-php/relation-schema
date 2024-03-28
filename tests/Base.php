<?php

namespace Amethyst\Tests;

abstract class Base extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
    }

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }

    protected function getPackageProviders($app)
    {
        return [
            \Amethyst\Providers\RelationSchemaServiceProvider::class,
            \Amethyst\Providers\FooServiceProvider::class,
        ];
    }
}
