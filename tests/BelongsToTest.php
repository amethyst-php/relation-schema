<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class BelongsToTest extends BaseTest
{
    public function startingBelongsTo($column, $params = [])
    {
        Schema::table('foo', function (BluePrint $table) use ($column) {
            if (Schema::hasColumn('foo', 'parent_id')) {
                $table->dropColumn($column);
            }
        });

        Schema::table('foo', function (Blueprint $table) use ($column) {
            $table->integer($column)->unsigned()->nullable();
        });

        RelationSchema::create([
            'name'    => 'parent',
            'type'    => 'BelongsTo',
            'data'    => 'foo',
            'payload' => Yaml::dump(array_merge($params, [
                'target' => 'foo',
            ])),
        ]);
    }

    public function testBelongsToWithTarget()
    {
        $this->startingBelongsTo('parent_id', [
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $child->parent()->associate($parent);
        $child->save();

        $this->assertEquals('Parent', $child->parent->name);
        $this->assertEquals("select * from `foo` where `foo`.`id` = '1' and `foo`.`deleted_at` is null", $this->getQuery($child->parent()));
    }

    public function testBelongsToWithTargetAndForeignKey()
    {
        $this->startingBelongsTo('customfield', [
            'foreignKey' => 'customfield'
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $child->parent()->associate($parent);
        $child->save();

        $this->assertEquals('Parent', $child->parent->name);
        $this->assertEquals("select * from `foo` where `foo`.`id` = '1' and `foo`.`deleted_at` is null", $this->getQuery($child->parent()));

        $third = Foo::create(['name' => 'Third']);
        $third->customfield = $child->id;
        $third->save();

        $this->assertEquals('Child', $third->parent->name);
    }
}
