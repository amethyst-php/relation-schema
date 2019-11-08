<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Illuminate\Database\Eloquent\Relations\Relation;

class MorphToMany extends Base
{
	protected $name = 'morph_to_many';

	public function define(RelationSchema $relationSchema)
	{
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getEntityClass($relationSchema->data);
        $target = $this->getEntityClass($payload->require('target'));
        $method = $this->getName();

        Relation::morphMap([
            $relationSchema->data => $data,
            $payload->require('target') => $target
        ]);

        $data::$method(
            $relationSchema->name,
            $target,
            $payload->get('name', 'target'),
            $payload->get('table', config('amethyst.relation.data.relation.table')),
            $payload->get('foreignPivotKey', 'target_id'),
            $payload->get('relatedPivotKey', 'source_id')
        )
        ->using($payload->get('using', config('amethyst.relation.data.relation.model')))
        ->withPivotValue('source_type', $relationSchema->data);
	}
}