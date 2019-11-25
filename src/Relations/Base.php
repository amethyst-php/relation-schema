<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Symfony\Component\Yaml\Yaml;
use Railken\Bag;
use Illuminate\Database\Eloquent\Model;
use Railken\EloquentMapper\Scopes\FilterScope;

class Base
{
	use \Amethyst\Concerns\GetClassNameByDataName;

	public function getName()
	{
		return $this->name;
	}

	public function extractPayload(RelationSchema $relationSchema)
	{
		return new Bag(Yaml::parse($relationSchema->payload));
	}

	public function filter($relation, $class, $filter)
	{
        app('amethyst')->filter(
            $query,
            $filter,
            new $class,
            new \Railken\Lem\Agents\SystemAgent()
        );
	}
}