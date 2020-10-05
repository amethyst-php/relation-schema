<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;

class MorphToMany extends Base
{
    protected $name = 'morph_to_many';

    public function define(RelationSchema $relationSchema)
    {
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getInstanceModelByName($relationSchema->data);
        $target = $this->getInstanceModelByName($payload->require('target'));
        $method = $this->getName();

        if ($payload->get('inverse', false) === false) {
            $relation = $data->$method(
                $relationSchema->name,
                $target,
                $payload->get('name', 'source'),
                $payload->get('table', config('amethyst.relation.data.relation.table')),
                $payload->get('relatedPivotKey', 'source_id'),
                $payload->get('foreignPivotKey', 'target_id')
            )
            ->withPivotValue('target_type', $payload->require('target'));
        } else {
            $relation = $data->$method(
                $relationSchema->name,
                $target,
                $payload->get('name', 'source'),
                $payload->get('table', config('amethyst.relation.data.relation.table')),
                $payload->get('foreignPivotKey', 'target_id'),
                $payload->get('relatedPivotKey', 'source_id'),
                null,
                null,
                true
            )
            ->withPivotValue('target_type', $relationSchema->data);
        }

        $relation
            ->using($payload->get('using', config('amethyst.relation.data.relation.model')))
            ->withPivotValue('key', $payload->get('key', $relationSchema->name));

        if ($payload->get('inversedBy')) {
            app('eloquent.mapper')->addInversedRelation(
                $data,
                $target,
                $relationSchema->name,
                $payload->get('inversedBy')
            );
            // $relation->mappedBy($payload->get('inversedBy'));
        }
        $this->filterTarget($relation, $target, $payload->get('filter'));
    }
}
