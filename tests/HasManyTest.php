<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class HasManyTest extends Base
{
    public function startingHasMany($column, $params = [])
    {
        Schema::table('foo', function (BluePrint $table) use ($column) {
            if (Schema::hasColumn('foo', $column)) {
                $table->dropColumn($column);
            }
        });

        Schema::table('foo', function (Blueprint $table) use ($column) {
            $table->integer($column)->unsigned()->nullable();
        });

        RelationSchema::create([
            'name'    => 'children',
            'type'    => 'HasMany',
            'data'    => 'foo',
            'payload' => Yaml::dump(array_merge($params, [
                'target' => 'foo',
            ])),
        ]);
    }

    public function testHasManySimple()
    {
        $this->startingHasMany('foo_id', [
        ]);

        Foo::truncate();

        $parent = Foo::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $parent->children()->save($child);

        $this->assertEquals($child->name, $parent->children->first()->name);
        $this->assertEquals(
            sprintf("select * from `foo` where `foo`.`foo_id` = '%s' and `foo`.`foo_id` is not null and `foo`.`deleted_at` is null", $parent->id),
            $this->getQuery($parent->children())
        );
    }

    public function testHasManyWithLocalKey()
    {
        $this->startingHasMany('foo_id', [
             'localKey' => 'localKey',
        ]);

        Foo::truncate();

        $parent = Foo::create(['name' => 'Parent']);
        $parent->localKey = 2;
        $child = Foo::create(['name' => 'Child']);

        $parent->children()->save($child);

        $this->assertEquals($child->name, $parent->children->first()->name);
        $this->assertEquals(
            sprintf("select * from `foo` where `foo`.`foo_id` = '%s' and `foo`.`foo_id` is not null and `foo`.`deleted_at` is null", $parent->localKey),
            $this->getQuery($parent->children())
        );
    }

    public function testHasManyWithFilter()
    {
        $this->startingHasMany('parent_id', [
            'foreignKey' => 'parent_id',
        ]);

        RelationSchema::create([
            'name'    => 'redChildren',
            'type'    => 'HasMany',
            'data'    => 'foo',
            'payload' => Yaml::dump([
                'target'     => 'foo',
                'foreignKey' => 'parent_id',
                'filter'     => "name ct 'Red' and name != 'Blue'",
            ]),
        ]);

        Foo::truncate();

        $grandParent = Foo::create(['name' => 'Grand Parent']);
        $parent = Foo::create(['name' => 'Parent']);
        $redChild1 = Foo::create(['name' => 'Red 1']);
        $redChild2 = Foo::create(['name' => 'Red 2']);
        $blueChild = Foo::create(['name' => 'Blue']);

        $grandParent->children()->save($parent);
        $parent->children()->save($redChild1);
        $parent->children()->save($redChild2);
        $parent->children()->save($blueChild);

        $this->assertEquals($redChild1->name, $parent->children[0]->name);
        $this->assertEquals($redChild2->name, $parent->children[1]->name);
        $this->assertEquals($blueChild->name, $parent->children[2]->name);
        $this->assertEquals(
            "select * from `foo` where `foo`.`parent_id` = '2' and `foo`.`parent_id` is not null and `foo`.`id` in (select `foo`.`id` from `foo` where (`foo`.`name` like '%Red%' and `foo`.`name` != 'Blue')) and `foo`.`deleted_at` is null",
            $this->getQuery($parent->redChildren())
        );
        $this->assertEquals(2, $parent->redChildren->count());
        $this->assertEquals(0, $grandParent->redChildren->count());
    }
}
