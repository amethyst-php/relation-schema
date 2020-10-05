<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class BelongsTo extends Base
{
    protected $name = 'belongs_to';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getInstanceModelByName($relationSchema->data);
        $target = $this->getInstanceModelByName($payload->require('target'));
        $foreignKey = $payload->get('foreignKey');
        $method = $this->getName();

        $relation = $data->$method(
            $relationSchema->name,
            $target,
            $foreignKey
        );
    }
}
