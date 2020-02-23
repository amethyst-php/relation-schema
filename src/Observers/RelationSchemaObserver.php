<?php

namespace Amethyst\Observers;

use Amethyst\Models\RelationSchema;

class RelationSchemaObserver
{
    /**
     * Handle the RelationSchema "created" event.
     *
     * @param \Amethyst\Models\RelationSchema $relation
     */
    public function created(RelationSchema $relation)
    {
        app('amethyst.relation-schema')->set($relation);
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
                app('amethyst.relation-schema')->unset($relation, $oldName);
            }
        }

        app('amethyst.relation-schema')->set($relation);
    }

    /**
     * Handle the RelationSchema "deleted" event.
     *
     * @param \Amethyst\Models\RelationSchema $relation
     */
    public function deleted(RelationSchema $relation)
    {
        app('amethyst.relation-schema')->unset($relation);
    }
}
