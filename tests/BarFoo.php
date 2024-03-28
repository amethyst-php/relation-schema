<?php

namespace Amethyst\Tests;

class BarFoo extends Base
{
    public function startingMorphTo($columnId, $columnType, $params = [])
    {
        Schema::table('foo', function (BluePrint $table) use ($columnId, $columnType) {
            if (Schema::hasColumn('foo', 'parent_id')) {
                $table->dropColumn($columnId);
                $table->dropColumn($columnType);
            }
        });

        Schema::table('foo', function (Blueprint $table) use ($columnId, $columnType) {
            $table->integer($columnId)->unsigned()->nullable();
            $table->string($columnType)->nullable();
        });

        RelationSchema::create([
            'name'    => 'parent',
            'type'    => 'MorphTo',
            'data'    => 'foo',
            'payload' => Yaml::dump(array_merge($params, [
                'target' => 'foo',
            ])),
        ]);
    }

    public function testMorphToBasic()
    {
        $this->startingMorphTo('parent_id', 'parent_type', [
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
        $this->startingMorphTo('pi', 'pt', [
            'foreignKey' => 'pt',
            'ownerKey'   => 'pi',
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
}
