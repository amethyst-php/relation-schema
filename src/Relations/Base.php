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
        $filter = new FilterScope(
            function (Model $model) use ($class) {
                return app('amethyst')->newManagerByModel(
                    $class,
                    new \Railken\Lem\Agents\SystemAgent()
                )->getAttributes()
                ->map(function ($attribute) {
                    return $attribute->getName();
                })->values()->toArray();
            },
            $filter, 
            [],
            []
        );

        $filter->apply($relation, new $class);
	}
}