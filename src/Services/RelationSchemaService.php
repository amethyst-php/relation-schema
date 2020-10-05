<?php

namespace Amethyst\Services;

use Amethyst\Concerns\GetInstanceModelByName;
use Amethyst\Core\Exceptions\DataNotFoundException;
use Amethyst\Models\RelationSchema;
use Amethyst\Relations\Base;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class RelationSchemaService
{
    use GetInstanceModelByName;

    /**
     * @var Container
     */
    protected $app;

    /**
     * Create a new instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Load all relations from the database.
     */
    public function boot()
    {
        if (!Schema::hasTable(Config::get('amethyst.relation-schema.data.relation-schema.table'))) {
            return;
        }

        foreach (RelationSchema::all() as $relation) {
            try {
                $this->set($relation, false);
            } catch (DataNotFoundException $e) {
                // Ignore data not found error
            }
        }
    }

    /**
     * Trigger an event update as a new relation has been added/removed/altered.
     *
     * @param $target
     */
    public function generate($target)
    {
        event(new \Railken\EloquentMapper\Events\EloquentMapUpdate($target));
    }

    /**
     * Retrieve new instance of relation by type.
     *
     * @param string $type
     *
     * @return Base
     */
    public function getRelationByType(string $type): Base
    {
        return $this->app->make('RelationSchema:'.$type);
    }

    /**
     * Set a new relation.
     *
     * @param RelationSchema $relationSchema
     * @param bool           $event
     */
    public function set(RelationSchema $relationSchema, bool $event = true)
    {
        $relation = $this->getRelationByType($relationSchema->type);

        $relation->define($relationSchema);

        if ($event) {
            $this->generate($relationSchema->data);
        }
    }

    /**
     * Unset a relation.
     *
     * @param RelationSchema $relation
     * @param string         $oldName
     */
    public function unset(RelationSchema $relation, string $oldName = null)
    {
        $model = $this->getInstanceModelByName($relation->data);

        if (!$model || !(new $model())->hasDynamicRelation($oldName ? $oldName : $relation->name)) {
            // Silent error, no needs to interrupt application for user-error
            return;
        }

        $model->removeRelation($oldName ? $oldName : $relation->name);

        $this->generate($relation->data);
    }
}
