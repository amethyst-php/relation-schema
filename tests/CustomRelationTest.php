<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\RelationSchema;
use Symfony\Component\Yaml\Yaml;

class CustomRelationTest extends BaseTest
{
    public function testBasicMorph()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Foo::create(['name' => 'Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_foos` inner join `amethyst_relations` on `amethyst_foos`.`id` = `amethyst_relations`.`source_id` where `amethyst_relations`.`target_id` = '1' and `amethyst_relations`.`target_type` = 'foo' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_relations`.`key` = 'foo:children' and `amethyst_foos`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('Child', $parent->children()->first()->name);
    }

    public function testBasicMorphCustomKey()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
                'key' => 'customKey'
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Foo::create(['name' => 'Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_foos` inner join `amethyst_relations` on `amethyst_foos`.`id` = `amethyst_relations`.`source_id` where `amethyst_relations`.`target_id` = '1' and `amethyst_relations`.`target_type` = 'foo' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_relations`.`key` = 'customKey' and `amethyst_foos`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('Child', $parent->children()->first()->name);
    }

    public function testBasicMorphCustomSubScope()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
                'key' => 'foo:children',
            ])
        ]);

        RelationSchema::create([
            'name'   => 'childrenChild',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
                'key' => 'foo:children',
                'filter' => 'name ct "Child"'
            ])
        ]);

        RelationSchema::create([
            'name'   => 'childrenFlux',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'foo',
                'key' => 'foo:children',
                'filter' => 'name ct "Flux"'
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Foo::create(['name' => 'A Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_foos` inner join `amethyst_relations` on `amethyst_foos`.`id` = `amethyst_relations`.`source_id` where `amethyst_relations`.`target_id` = '1' and `amethyst_relations`.`target_type` = 'foo' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_relations`.`key` = 'foo:children' and `amethyst_foos`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('A Child', $parent->children()->first()->name);

        $this->assertEquals(1, $parent->childrenChild->count());
        $this->assertEquals(0, $parent->childrenFlux->count());
    }

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }
}
