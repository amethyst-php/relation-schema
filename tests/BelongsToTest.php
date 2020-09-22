<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class BelongsToTest extends BaseTest
{
    public function testBelongsToWithTarget()
    {
        Schema::table('foo', function (BluePrint $table) {
            if (Schema::hasColumn('foo', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
        });

        Schema::table('foo', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->nullable();
        });

        RelationSchema::create([
            'name'    => 'parent',
            'type'    => 'BelongsTo',
            'data'    => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
            ]),
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
        Schema::table('foo', function (BluePrint $table) {
            if (Schema::hasColumn('foo', 'customfield')) {
                $table->dropColumn('customfield');
            }
        });

        Schema::table('foo', function (Blueprint $table) {
            $table->integer('customfield')->unsigned()->nullable();
        });

        RelationSchema::create([
            'name'    => 'parent',
            'type'    => 'BelongsTo',
            'data'    => 'foo',
            'payload' => Yaml::dump([
                'target'     => 'foo',
                'foreignKey' => 'customfield',
            ]),
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

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }
}
