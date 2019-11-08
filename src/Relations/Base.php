<?php

namespace Amethyst\Relations;

use Amethyst\Models\RelationSchema;
use Symfony\Component\Yaml\Yaml;
use Railken\Bag;

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
}