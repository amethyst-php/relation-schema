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

        $relation = $data::$method(
            $relationSchema->name,
            $target,
            $payload->get('name', 'source'),
            $payload->get('table', config('amethyst.relation.data.relation.table')),
            $payload->get('relatedPivotKey', 'source_id'),
            $payload->get('foreignPivotKey', 'target_id')
        )
        ->using($payload->get('using', config('amethyst.relation.data.relation.model')))
        ->withPivotValue('target_type', $payload->require('target'))
        ->withPivotValue('key', $payload->get('key', $relationSchema->data.":".$relationSchema->name));

        app('amethyst')->pushMorphRelation('relation', 'source', $relationSchema->data);
        app('amethyst')->pushMorphRelation('relation', 'target', $payload->require('target'));

        if (!empty($payload->get('filter'))) {
            $qb = new \Amethyst\CallCatcher;
        	$this->filter($qb, $target, $payload->get('filter'));
            
            foreach ($qb->calls as $call) {

                $method = $call[0];
                if (!in_array($method, ['getQuery', 'from'])) {
                    $relation->$method(...$call[1]);
                }
            }
    	}
	}
}