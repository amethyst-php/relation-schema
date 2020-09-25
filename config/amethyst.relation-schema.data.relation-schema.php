<?php

return [
    'table'      => 'relation_schema',
    'comment'    => 'RelationSchema',
    'model'      => Amethyst\Models\RelationSchema::class,
    'schema'     => Amethyst\Schemas\RelationSchemaSchema::class,
    'repository' => Amethyst\Repositories\RelationSchemaRepository::class,
    'serializer' => Amethyst\Serializers\RelationSchemaSerializer::class,
    'validator'  => Amethyst\Validators\RelationSchemaValidator::class,
    'authorizer' => Amethyst\Authorizers\RelationSchemaAuthorizer::class,
    'faker'      => Amethyst\Fakers\RelationSchemaFaker::class,
    'manager'    => Amethyst\Managers\RelationSchemaManager::class,
    'attributes' => [
        'type' => [
            'options' => [
                'BelongsTo'   => 'BelongsTo',
                'HasMany'     => 'HasMany',
                'MorphTo'     => 'MorphTo',
                'MorphToOne'  => 'MorphToOne',
                'MorphToMany' => 'MorphToMany',
                'MorphMany'   => 'MorphMany',
            ],
        ],
    ],
];
