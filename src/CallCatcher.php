<?php

namespace Amethyst;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Catch all calls made to the query builder.
 */
class CallCatcher
{
    /**
     * @var array
     */
    public $calls;

    /**
     * Create a new instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->instance = $model->newQuery();
        $this->model = $model;
        $this->query = $this->instance->getQuery();
    }

    /**
     * Register all calls.
     *
     * @param string $method
     * @param array  $parameters
     */
    public function __call(string $method, array $parameters)
    {
        $this->calls[] = [$method, $parameters];

        return $this->instance->$method(...$parameters);
    }
}
