<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Symfony\Component\Yaml\Yaml;

class CustomRelationTest extends BaseTest
{
    public function testMorphManyRelation()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo'
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Foo::create(['name' => 'Child']);

        $parent->children()->attach($children);

        // Testing parent
        $this->assertEquals('Parent', $parent->name);

        // Testing Red
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_foos` inner join `amethyst_relations` on `amethyst_foos`.`id` = `amethyst_relations`.`source_id` where `amethyst_relations`.`target_id` = '1' and `amethyst_relations`.`target_type` = 'foo' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_foos`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('Child', $parent->children()->first()->name);
    }

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }
}
