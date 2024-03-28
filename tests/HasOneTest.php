<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class HasOneTest extends Base
{
    public function startingHasMany($column, $params = [])
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
            'name'    => 'children',
            'type'    => 'HasOne',
            'data'    => 'foo',
            'payload' => Yaml::dump(array_merge($params, [
                'target' => 'foo',
            ])),
        ]);
    }

    public function testHasManyWithTarget()
    {
        $this->startingHasMany('foo_id', [
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $child = Foo::create(['name' => 'Child']);

        $parent->children()->save($child);

        $this->assertEquals($child->name, $parent->children->name);
        $this->assertEquals(
            "select * from `foo` where `foo`.`foo_id` = '1' and `foo`.`foo_id` is not null and `foo`.`deleted_at` is null",
            $this->getQuery($parent->children())
        );
    }

    public function testHasManyWithTargetAndLocalKey()
    {
        $this->startingHasMany('foo_id', [
             'localKey' => 'localKey',
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $parent->localKey = 2;
        $child = Foo::create(['name' => 'Child']);

        $parent->children()->save($child);

        $this->assertEquals($child->name, $parent->children->name);
        $this->assertEquals(
            "select * from `foo` where `foo`.`foo_id` = '2' and `foo`.`foo_id` is not null and `foo`.`deleted_at` is null",
            $this->getQuery($parent->children())
        );
    }
}
