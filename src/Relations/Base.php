<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Illuminate\Database\Eloquent\Model;
use Railken\Bag;
use Railken\EloquentMapper\Scopes\FilterScope;
use Symfony\Component\Yaml\Yaml;

abstract class Base
{
    use \Amethyst\Concerns\GetClassNameByDataName;

    public function getName()
    {
        return $this->name;
    }

    abstract public function define(RelationSchema $relationSchema);

    public function extractPayload(RelationSchema $relationSchema)
    {
        return new Bag(empty($relationSchema->payload) ? [] : Yaml::parse($relationSchema->payload));
    }

    public function filterTarget($relation, Model $target, $filter)
    {
        if (empty($filter)) {
            return;
        }

        $qb = new \Amethyst\CallCatcher($target);

        $this->filter($qb, $filter);

        return $relation->whereIn($target->getTable().'.'.$target->getKeyName(), function ($query) use ($qb, $target) {
            $query->from($target->getTable());

            foreach ($qb->calls as $call) {
                $method = $call[0];

                if (!in_array($method, [
                    'getModel',
                    'getQuery',
                    'from',
                    'select',
                ], true)) {
                    $query->$method(...$call[1]);
                }
            }

            $query->select($target->getTable().'.'.$target->getKeyName());
        });
    }

    public function filter($query, $filter)
    {
        $scope = new FilterScope();

        $scope->apply($query, strval($filter));
    }
}
