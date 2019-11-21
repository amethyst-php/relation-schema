<?php

namespace Amethyst\Tests;

use Amethyst\Models\Foo;
use Amethyst\Models\Bar;
use Amethyst\Models\RelationSchema;
use Symfony\Component\Yaml\Yaml;

class CustomRelationTest extends BaseTest
{
    public function testBasicMorphDef()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'bar',
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Bar::create(['name' => 'Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_bars` inner join `amethyst_relations` on `amethyst_bars`.`id` = `amethyst_relations`.`target_id` where `amethyst_relations`.`source_id` = '1' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_relations`.`target_type` = 'bar' and `amethyst_relations`.`key` = 'foo:children' and `amethyst_bars`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('Child', $parent->children()->first()->name);
    }

    public function testBasicMorphCustomKey()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'bar',
                'key' => 'customKey'
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Bar::create(['name' => 'Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_bars` inner join `amethyst_relations` on `amethyst_bars`.`id` = `amethyst_relations`.`target_id` where `amethyst_relations`.`source_id` = '1' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_relations`.`target_type` = 'bar' and `amethyst_relations`.`key` = 'customKey' and `amethyst_bars`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('Child', $parent->children()->first()->name);
    }

    public function testBasicMorphCustomSubScope()
    {
        RelationSchema::create([
            'name'   => 'children',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'bar',
                'key' => 'bar:children',
            ])
        ]);

        RelationSchema::create([
            'name'   => 'childrenChild',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'bar',
                'key' => 'bar:children',
                'filter' => 'name ct "Child"'
            ])
        ]);

        RelationSchema::create([
            'name'   => 'childrenFlux',
            'type'   => 'MorphToMany',
            'data' => 'foo',
            'payload' => Yaml::dump([
                'target' => 'bar',
                'key' => 'bar:children',
                'filter' => 'name ct "Flux"'
            ])
        ]);

        $parent = Foo::create(['name' => 'Parent']);
        $children = Bar::create(['name' => 'A Child']);

        $parent->children()->attach($children);

        $this->assertEquals('Parent', $parent->name);
        $this->assertEquals(1, $parent->children->count());
        $this->assertEquals("select * from `amethyst_bars` inner join `amethyst_relations` on `amethyst_bars`.`id` = `amethyst_relations`.`target_id` where `amethyst_relations`.`source_id` = '1' and `amethyst_relations`.`source_type` = 'foo' and `amethyst_relations`.`target_type` = 'bar' and `amethyst_relations`.`key` = 'bar:children' and `amethyst_bars`.`deleted_at` is null", $this->getQuery($parent->children()));
        $this->assertEquals('A Child', $parent->children()->first()->name);

        $this->assertEquals(1, $parent->childrenChild->count());
        $this->assertEquals(0, $parent->childrenFlux->count());
    }

    public function getQuery($builder)
    {
        return vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings());
    }
}
