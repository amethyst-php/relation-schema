<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class MorphMany extends Base
{
    protected $name = 'morph_many';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getEntityClass($relationSchema->data);
        $target = $this->getEntityClass($payload->require('target'));
        $method = $this->getName();

        $relation = $data::$method(
            $relationSchema->name,
            $target,
            $payload->get('morphType', $payload->require('target'))
        );

        $this->filterTarget($relation, $target, $payload->get('filter'));
    }
}
