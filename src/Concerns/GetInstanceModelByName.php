<?php

namespace Amethyst\Concerns;

use Railken\EloquentMapper\Contracts\Map as MapContract;

trait GetInstanceModelByName
{
    public function getInstanceModelByName(string $name)
    {
        return app(MapContract::class)->keyToModel($name);
    }
}
