<?php

namespace Amethyst;

class CallCatcher
{
	public $calls;
	
    public function __call($method, $args)
    {
        $this->calls[] = [$method, $args];

        return $this;
    }
}