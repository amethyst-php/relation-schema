<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class HasMany extends Base
{
    protected $name = 'has_many';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getEntityClass($relationSchema->data);
        $target = $this->getEntityClass($payload->require('target'));
        $method = $this->getName();

        $relation = $data->$method(
            $relationSchema->name,
            $target,
            $payload->get('foreignKey'),
            $payload->get('localKey')
        );

        $this->filterTarget($relation, $target, $payload->get('filter'));
    }
}
