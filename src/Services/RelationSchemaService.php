<?php

namespace Amethyst\Services;

use Amethyst\Models\RelationSchema;
use Illuminate\Database\Eloquent\Relations\Relation;

class RelationSchemaService
{
    public function boot()
    {
        RelationSchema::all()->map(function (RelationSchema $relation) {
            $this->set($relation, false);
        });
    }

    public function generate($target)
    {
        event(new \Railken\EloquentMapper\Events\EloquentMapUpdate($target));
    }

    public function set(RelationSchema $relation, bool $event = true)
    {
        $source = $this->getEntityClass($relation->source);
        $target = $this->getEntityClass($relation->target);

        if (!$source || !$target) {
            // Silent error, no needs to interrupt application for user-error
            return;
        }

        Relation::morphMap([
            $relation->source => $source,
            $relation->target => $target,
        ]);

        $source::morph_to_many(
            $relation->name, 
            $target, 
            'target', 
            config('amethyst.relation.data.relation.table'), 
            'target_id', 
            'source_id'
        )
        ->using(config('amethyst.relation.data.relation.model'))
        ->withPivotValue('key', $relation->filter)
        ->withPivotValue('source_type', $relation->source);


        // Define inverse relationship if $relation->inverse is not null (contains name)
        if ($event) {
            $this->generate($relation->source);
        }
    }

    public function unset(RelationSchema $relation, string $oldName = null)
    {
        $model = $this->getEntityClass($relation->source);
        $target = $this->getEntityClass($relation->target);

        if (!$model || !$target) {

            // Silent error, no needs to interrupt application for user-error
            return;
        }

        $model::removeRelation($oldName ? $oldName : $relation->name);

        $this->generate($relation->source);
    }

    public function getEntityClass(string $name)
    {
        return app('amethyst')->findModelByName($name);
    }
}
