<?php

namespace Amethyst\Providers;

use Amethyst\Core\Providers\CommonServiceProvider;
use Amethyst\Models\RelationSchema;
use Amethyst\Observers\RelationSchemaObserver;
use Illuminate\Container\Container;

class RelationSchemaServiceProvider extends CommonServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        parent::register();

        $this->app->register(\Amethyst\Providers\RelationServiceProvider::class);

        $this->app->singleton('amethyst.relation-schema', function (Container $app) {
            return new \Amethyst\Services\RelationSchemaService($app);
        });

        $this->app->bind('RelationSchema:BelongsTo', \Amethyst\Relations\BelongsTo::class);
        $this->app->bind('RelationSchema:HasMany', \Amethyst\Relations\HasMany::class);
        $this->app->bind('RelationSchema:HasOne', \Amethyst\Relations\HasOne::class);
        $this->app->bind('RelationSchema:BelongsToMany', \Amethyst\Relations\BelongsToMany::class);
        $this->app->bind('RelationSchema:MorphToMany', \Amethyst\Relations\MorphToMany::class);
        $this->app->bind('RelationSchema:MorphToOne', \Amethyst\Relations\MorphToOne::class);
        $this->app->bind('RelationSchema:MorphMany', \Amethyst\Relations\MorphMany::class);
        $this->app->bind('RelationSchema:MorphTo', \Amethyst\Relations\MorphTo::class);
    }

    /**
     * @inherit
     */
    public function boot()
    {
        parent::boot();

        RelationSchema::observe(RelationSchemaObserver::class);

        app('amethyst.relation-schema')->boot();
    }
}
