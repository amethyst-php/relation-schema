<?php

namespace Amethyst\Tests;

use Amethyst\Models\Bar;
use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class BelongsToManyTest extends BaseTest
{
    public function startingBelongsToMany($tableName = 'bar_foo')
    {
        if (Schema::hasTable($tableName)) {
            Schema::dropTable($tableName);
        }

        Schema::create($tableName, function (BluePrint $table) {
            $table->integer('foo_id')->unsigned();
            $table->integer('bar_id')->unsigned();
            $table->integer('value')->nullable();
        });
    }

    public function testBasic()
    {
        $this->startingBelongsToMany();

        RelationSchema::create([
            'name'    => 'children',
            'type'    => 'BelongsToMany',
            'data'    => 'foo',
            'payload' => Yaml::dump([
                'target' => 'bar',
            ]),
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Bar::create(['name' => 'Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `bar` inner join `bar_foo` on `bar`.`id` = `bar_foo`.`bar_id` where `bar_foo`.`foo_id` = '1' and `bar`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('Child', $parent->children()->first()->name);
    }
}
