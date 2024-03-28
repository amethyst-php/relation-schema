<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class MorphTo extends Base
{
    protected $name = 'morphTo';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);
        $data = $this->getInstanceModelByName($relationSchema->data);

        $data::resolveRelationUsing($relationSchema->name, function ($model) use ($payload, $relationSchema) {

            $foreignKey = $payload->get('foreignKey', $relationSchema->name);
            $ownerKey = $payload->get('keyName');
            $type = $payload->get('keyId');
            $method = $this->getName();

            $relation = $model->$method(
                $foreignKey,
                $ownerKey,
                $type
            );

            return $relation;
        });
    }
}
