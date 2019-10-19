<?php

namespace Amethyst\Services;

use Amethyst\Models\RelationSchema;
use Illuminate\Database\Eloquent\Relations\Relation;

class RelationSchemaService
{
    public function boot()
    {
        foreach (RelationSchema::all() as $relation) {
            $this->set($relation, false);
        }
    }

    public function generate($target)
    {
        event(new \Railken\EloquentMapper\Events\EloquentMapUpdate($target));
    }

    public function set(RelationSchema $relation, bool $event = true)
    {
        $source = $this->getEntityClass($relation->source);
        $target = $this->getEntityClass($relation->target);

        $methods = [
            'MorphToMany' => 'morph_to_many',
            'MorphToOne' => 'morph_to_one'
        ];

        $method = $methods[$relation->type] ?? null;

        if (!$method || !$source || !$target) {

            // Silent error, no needs to interrupt application for user-error
            return;
        }

        Relation::morphMap([
            $relation->source => $source,
            $relation->target => $target,
        ]);

        if (!$relation->inverse) {
            $source::$method(
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
        } else {
            $source::$method(
                $relation->name,
                $source,
                'source',
                config('amethyst.relation.data.relation.table'),
                'source_id',
                'target_id'
            )
            ->using(config('amethyst.relation.data.relation.model'))
            ->withPivotValue('key', $relation->filter)
            ->withPivotValue('target_type', $relation->target);
        }

        if ($event) {
            $this->generate($relation->source);
        }
    }

    public function unset(RelationSchema $relation, string $oldName = null)
    {
        $model = $this->getEntityClass($relation->source);
        $target = $this->getEntityClass($relation->target);

        if (!$model || !$target || !(new $model)->hasRelation($oldName ? $oldName : $relation->name)) {
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
