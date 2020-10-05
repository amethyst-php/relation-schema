<?php

namespace Amethyst\Observers;

use Amethyst\Models\RelationSchema;
use Illuminate\Container\Container;

class RelationSchemaObserver
{   
    /**
     * @var Container
     */
    protected $app;

    /**
     * Create a new instance
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Handle the RelationSchema "created" event.
     *
     * @param \Amethyst\Models\RelationSchema $relation
     */
    public function created(RelationSchema $relation)
    {
        $this->app->get('amethyst.relation-schema')->set($relation);
    }

    /**
     * Handle the RelationSchema "updated" event.
     *
     * @param \Amethyst\Models\RelationSchema $relation
     */
    public function updated(RelationSchema $relation)
    {
        if (isset($relation->getOriginal()['name'])) {
            $oldName = $relation->getOriginal()['name'];

            if ($relation->name !== $oldName) {
                $this->app->get('amethyst.relation-schema')->unset($relation, $oldName);
            }
        }

        $this->app->get('amethyst.relation-schema')->set($relation);
    }

    /**
     * Handle the RelationSchema "deleted" event.
     *
     * @param \Amethyst\Models\RelationSchema $relation
     */
    public function deleted(RelationSchema $relation)
    {
        $this->app->get('amethyst.relation-schema')->unset($relation);
    }
}
