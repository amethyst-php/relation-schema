<?php

namespace Amethyst\Concerns;

trait GetClassNameByDataName
{
    public function getEntityClass(string $name)
    {
        return app('amethyst')->findModelByName($name);
    }
}