<?php

namespace Amethyst\Providers;

use Amethyst\Common\CommonServiceProvider;
use Amethyst\Models\RelationSchema;
use Amethyst\Observers\RelationSchemaObserver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class RelationSchemaServiceProvider extends CommonServiceProvider
{
    /**
     * @inherit
     */
    public function register()
    {
        parent::register();

        $this->app->register(\Amethyst\Providers\RelationServiceProvider::class);

        $this->app->singleton('amethyst.relation-schema', function ($app) {
            return new \Amethyst\Services\RelationSchemaService();
        });
    }

    /**
     * @inherit
     */
    public function boot()
    {
        parent::boot();

        RelationSchema::observe(RelationSchemaObserver::class);

        if (Schema::hasTable(Config::get('amethyst.relation-schema.data.relation-schema.table'))) {
            app('amethyst.relation-schema')->boot();
        }
    }
}
