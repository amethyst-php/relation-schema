<?php

namespace Amethyst;

use Illuminate\Database\Eloquent\Model;

class CallCatcher
{
    public $calls;
    public $model;

    public function __call($method, $args)
    {
        $this->calls[] = [$method, $args];

        return $this;
    }

    public function setModel(Model $model)
    {
    	$this->model = $model;
    }

    public function getModel(): Model
    {
    	return $this->model;
    }
}
