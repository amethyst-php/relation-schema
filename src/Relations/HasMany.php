<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class HasMany extends Base
{
    protected $name = 'hasMany';

    public function define(RelationSchema $relationSchema)
    {

        $payload = $this->extractPayload($relationSchema);
        $data = $this->getInstanceModelByName($relationSchema->data);

        $data::resolveRelationUsing($relationSchema->name, function ($model) use ($payload) {

            $target = $this->getInstanceModelByName($payload->require('target'));
            $method = $this->getName();

            $relation = $model->$method(
                $target,
                $payload->get('foreignKey'),
                $payload->get('localKey')
            );

            $this->filterTarget($relation, $target, $payload->get('filter'));

            return $relation;
        });
    }
}
