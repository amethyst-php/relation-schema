<?php

return [
    'enabled'    => true,
    'controller' => Amethyst\Http\Controllers\Admin\RelationSchemasController::class,
    'router'     => [
        'as'     => 'relation-schema.',
        'prefix' => '/relation-schemas',
    ],
];
