<?php

namespace Amethyst\Tests;

use Amethyst\Models\Bar;
use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class MorphToTest extends BaseTest
{
    public function testMorphTo()
    {
        Schema::table('foo', function (BluePrint $table) {
            if (Schema::hasColumn('foo', 'parent_id')) {
                $table->dropColumn('parent_id');
                $table->dropColumn('parent_type');
            }
        });

        Schema::table('foo', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('parent_type')->nullable();
        });

        RelationSchema::create([
            'name' => 'parent',
            'type' => 'MorphTo',
            'data' => 'foo',
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $child->parent()->associate($parent);
        $child->save();

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals('foo', $child->parent_type);
        $this->assertEquals('Parent', $child->parent->name);
        $this->assertEquals("select * from `foo` where `foo`.`id` = '1' and `foo`.`deleted_at` is null", $this->getQuery($child->parent()));

        $parent = Bar::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $child->parent()->associate($parent);
        $child->save();

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals('bar', $child->parent_type);
        $this->assertEquals('Parent', $child->parent->name);
        $this->assertEquals("select * from `bar` where `bar`.`id` = '1' and `bar`.`deleted_at` is null", $this->getQuery($child->parent()));
    }

    public function testMorphToWithKeys()
    {
        Schema::table('foo', function (BluePrint $table) {
            if (Schema::hasColumn('foo', 'parent_id')) {
                $table->dropColumn('i');
                $table->dropColumn('pt');
            }
        });

        Schema::table('foo', function (Blueprint $table) {
            $table->integer('pi')->unsigned()->nullable();
            $table->string('pt')->nullable();
        });

        RelationSchema::create([
            'name'    => 'parent',
            'type'    => 'MorphTo',
            'data'    => 'foo',
            'payload' => Yaml::dump([
                'foreignKey' => 'pt',
                'ownerKey'   => 'pi',
            ]),
        ]);

        $parent = Bar::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $child->parent()->associate($parent);
        $child->save();

        $this->assertEquals($parent->id, $child->pi);
        $this->assertEquals('bar', $child->pt);
        $this->assertEquals('Parent', $child->parent->name);
        $this->assertEquals("select * from `bar` where `bar`.`id` = '1' and `bar`.`deleted_at` is null", $this->getQuery($child->parent()));
    }

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }
}
