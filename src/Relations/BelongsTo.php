<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class BelongsTo extends Base
{
    protected $name = 'belongsTo';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getInstanceModelByName($relationSchema->data);

        $data::resolveRelationUsing($relationSchema->name, function ($model) use ($payload, $relationSchema) {
            $target = $this->getInstanceModelByName($payload->require('target'));
            $foreignKey = $payload->get('foreignKey', $relationSchema->name."_id");
            $method = $this->getName();

            return $model->$method(
                $target,
                $foreignKey
            );
        });
    }
}
