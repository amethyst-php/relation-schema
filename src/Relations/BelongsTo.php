<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsTo extends Base
{
	protected $name = 'belongs_to';

	public function define(RelationSchema $relationSchema)
	{
        $payload = $this->extractPayload($relationSchema);

        $data = $this->getEntityClass($relationSchema->data);
        $target = $this->getEntityClass($payload->require('target'));
        $method = $this->getName();

        $relation = $data::$method(
            $relationSchema->name,
            $target
        );
	}
}