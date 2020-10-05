<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class MorphTo extends Base
{
    protected $name = 'morph_to';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getInstanceModelByName($relationSchema->data);
        $foreignKey = $payload->get('foreignKey');
        $ownerKey = $payload->get('ownerKey');
        $method = $this->getName();

        $relation = $data->$method(
            $relationSchema->name,
            $foreignKey,
            $ownerKey
        );
    }
}
