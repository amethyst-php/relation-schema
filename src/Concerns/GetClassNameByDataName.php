<?php

namespace Amethyst\Concerns;

trait GetClassNameByDataName
{
    public function getEntityClass(string $name)
    {
        return app('amethyst')->findManagerByName($name)->newEntity();
    }
}
