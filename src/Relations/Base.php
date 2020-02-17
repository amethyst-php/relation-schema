<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Illuminate\Database\Eloquent\Model;
use Railken\Bag;
use Railken\EloquentMapper\Scopes\FilterScope;
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

    public function filterTarget($relation, Model $target, $filter)
    {
        if (empty($filter)) {
            return;
        }

        $qb = new \Amethyst\CallCatcher();

        $qb->setModel($target);
        $this->filter($qb, $filter);

        foreach ($qb->calls as $call) {
            $method = $call[0];
            if (!in_array($method, ['getQuery', 'from'], true)) {
                $relation->$method(...$call[1]);
            }
        }
    }

    public function filter($query, $filter)
    {
        $scope = new FilterScope();

        $scope->apply($query, strval($filter));
    }
}
