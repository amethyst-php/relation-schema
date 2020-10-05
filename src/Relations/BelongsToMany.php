<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class BelongsToMany extends Base
{
    protected $name = 'belongs_to_many';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getInstanceModelByName($relationSchema->data);
        $target = $this->getInstanceModelByName($payload->require('target'));
        $method = $this->getName();

        $relation = $data->$method(
            $relationSchema->name,
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
    }
}
