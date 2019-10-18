<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;

class CustomRelationTest extends BaseTest
{
    public function testMorphManyRelation()
    {
        RelationSchema::create([
            'name'   => 'redChildren',
            'source' => 'foo',
            'target' => 'foo',
            'type'   => 'MorphMany',
            'filter' => 'redChildren',
        ]);

        RelationSchema::create([
            'name'   => 'blueChildren',
            'source' => 'foo',
            'target' => 'foo',
            'type'   => 'MorphMany',
            'filter' => 'blueChildren',
        ]);

        $parent = Foo::create(['name' => 'Parent']);

        $redChildren = Foo::create(['name' => 'Child:Red']);
        $blueChildren = Foo::create(['name' => 'Child:Blue']);

        $parent->redChildren()->attach($redChildren);
        $parent->blueChildren()->attach($blueChildren);

        // Testing parent
        $this->assertEquals('Parent', $parent->name);

        // Testing Red
        $this->assertEquals(1, $parent->redChildren->count());
        $this->assertEquals("select * from `amethyst_foos` inner join `amethyst_relations` on `amethyst_foos`.`id` = `amethyst_relations`.`source_id` where `amethyst_relations`.`target_id` = '1' and `amethyst_relations`.`target_type` = 'foo' and `amethyst_relations`.`key` = 'redChildren' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_foos`.`deleted_at` is null", $this->getQuery($parent->redChildren()));
        $this->assertEquals('Child:Red', $parent->redChildren()->first()->name);

        // Testing Blue
        $this->assertEquals(1, $parent->blueChildren->count());
        $this->assertEquals("select * from `amethyst_foos` inner join `amethyst_relations` on `amethyst_foos`.`id` = `amethyst_relations`.`source_id` where `amethyst_relations`.`target_id` = '1' and `amethyst_relations`.`target_type` = 'foo' and `amethyst_relations`.`key` = 'blueChildren' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_foos`.`deleted_at` is null", $this->getQuery($parent->blueChildren()));
        $this->assertEquals('Child:Blue', $parent->blueChildren()->first()->name);
    }

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }
}
