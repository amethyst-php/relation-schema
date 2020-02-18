<?php

namespace Amethyst\Services;

use Amethyst\Core\Exceptions\DataNotFoundException;
use Amethyst\Models\RelationSchema;
use Illuminate\Container\Container;

class RelationSchemaService
{
    use \Amethyst\Concerns\GetClassNameByDataName;

    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function boot()
    {
        foreach (RelationSchema::all() as $relation) {
            try {
                $this->set($relation, false);
            } catch (DataNotFoundException $e) {
                // Ignore data not found error
            }
        }
    }

    public function generate($target)
    {
        event(new \Railken\EloquentMapper\Events\EloquentMapUpdate($target));
    }

    public function getRelationByType(string $type)
    {
        return $this->app->make('RelationSchema:'.$type);
    }

    public function set(RelationSchema $relationSchema, bool $event = true)
    {
        $relation = $this->getRelationByType($relationSchema->type);

        $relation->define($relationSchema);

        if ($event) {
            $this->generate($relationSchema->data);
        }
    }

    public function unset(RelationSchema $relation, string $oldName = null)
    {
        $model = $this->getEntityClass($relation->data);

        if (!$model || !(new $model())->hasDynamicRelation($oldName ? $oldName : $relation->name)) {
            // Silent error, no needs to interrupt application for user-error
            return;
        }
        
        $model->removeRelation($oldName ? $oldName : $relation->name);

        $this->generate($relation->data);
    }
}
