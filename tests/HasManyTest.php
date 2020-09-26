<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class HasManyTest extends BaseTest
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

    public function testHasMany()
    {
        $this->startingHasMany('foo_id', [
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $parent->children()->save($child);

        $this->assertEquals($child->name, $parent->children->first()->name);
        $this->assertEquals(
            "select * from `foo` where `foo`.`foo_id` = '1' and `foo`.`foo_id` is not null and `foo`.`deleted_at` is null",
            $this->getQuery($parent->children())
        );
    }

    public function testHasManyWithLocalKey()
    {
        $this->startingHasMany('foo_id', [
             'localKey' => 'localKey',
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $parent->localKey = 2;
        $child = Foo::create(['name' => 'Child']);

        $parent->children()->save($child);

        $this->assertEquals($child->name, $parent->children->first()->name);
        $this->assertEquals(
            "select * from `foo` where `foo`.`foo_id` = '2' and `foo`.`foo_id` is not null and `foo`.`deleted_at` is null",
            $this->getQuery($parent->children())
        );
    }

    public function testHasManyWithFilter()
    {
        $this->startingHasMany('parent_id', [
            'foreignKey' => 'parent_id'
        ]);

        RelationSchema::create([
            'name'    => 'redChildren',
            'type'    => 'HasMany',
            'data'    => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
                'foreignKey' => 'parent_id',
                'filter' => "children.name eq 'Red' and children.name != 'Blue'"
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $redChild1 = Foo::create(['name' => 'Red 1']);
        $redChild2 = Foo::create(['name' => 'Red 2']);
        $blueChild = Foo::create(['name' => 'Blue']);

        $parent->children()->save($redChild1);
        $parent->children()->save($redChild2);
        $parent->children()->save($blueChild);

        $this->assertEquals($redChild1->name, $parent->children[0]->name);
        $this->assertEquals($redChild2->name, $parent->children[1]->name);
        $this->assertEquals($blueChild->name, $parent->children[2]->name);
        $this->assertEquals(
            "select `foo`.* from `foo` left join `foo` as `children` on `foo`.`id` = `children`.`parent_id` and `children`.`deleted_at` is null where (`children`.`name` = 'Red' and `children`.`name` != 'Blue') and `foo`.`deleted_at` is null",
            $this->getQuery($parent->redChildren())
        );
    }
}
