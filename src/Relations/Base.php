<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Railken\Bag;
use Symfony\Component\Yaml\Yaml;

class Base
{
    use \Amethyst\Concerns\GetClassNameByDataName;

    public function getName()
    {
        return $this->name;
    }

    public function extractPayload(RelationSchema $relationSchema)
    {
        return new Bag(Yaml::parse($relationSchema->payload));
    }

    public function filter($relation, $class, $filter)
    {
        app('amethyst')->filter(
            $query,
            $filter,
            new $class(),
            new \Railken\Lem\Agents\SystemAgent()
        );
    }
}
