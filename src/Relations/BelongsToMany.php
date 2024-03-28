<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class BelongsToMany extends Base
{
    protected $name = 'belongsToMany';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);
        $data = $this->getInstanceModelByName($relationSchema->data);

        $data::resolveRelationUsing($relationSchema->name, function ($model) use ($payload) {

            $target = $this->getInstanceModelByName($payload->require('target'));
            $method = $this->getName();

            $relation = $model->$method(
                $target,
                $payload->get('using') ? $this->getInstanceModelByName($payload->get('using'))->getTable() : null,
                $payload->get('foreignPivotKey'),
                $payload->get('relatedPivotKey'),
                $payload->get('parentKey'),
                $payload->get('relatedKey')
            );

            if ($payload->get('using')) {
                $relation->using($payload->get('using'));
            }

            $this->filterTarget($relation, $target, $payload->get('filter'));

            return $relation;

        });

    }
}
