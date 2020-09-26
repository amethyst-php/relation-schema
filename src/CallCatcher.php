<?php

namespace Amethyst;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CallCatcher
{
    public $calls;

    public function __construct($model)
    {
        $this->instance = $model->newQuery();
        $this->model = $model;
        $this->query = $this->instance->getQuery();
    }

    public function __call($method, $parameters)
    {
        $this->calls[] = [$method, $parameters];

        return $this->instance->$method(...$parameters);
    }
}
