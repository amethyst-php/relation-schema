<?php

namespace Amethyst\Schemas;

use Railken\Lem\Attributes;
use Railken\Lem\Schema;

class RelationSchemaSchema extends Schema
{
    /**
     * Get all the attributes.
     *
     * @var array
     */
    public function getAttributes()
    {
        return [
            Attributes\IdAttribute::make(),
            Attributes\TextAttribute::make('name')
                ->setRequired(true)
                ->setUnique(true),
            Attributes\LongTextAttribute::make('description'),
            Attributes\TextAttribute::make('source')
                ->setRequired(true),
            Attributes\TextAttribute::make('target')
                ->setRequired(true),
            Attributes\EnumAttribute::make('type', [
                'MorphOne'  => 'MorphOne',
                'MorphMany' => 'MorphMany',
            ])->setRequired(true),
            Attributes\TextAttribute::make('filter')
                ->setRequired(true),
            Attributes\CreatedAtAttribute::make(),
            Attributes\UpdatedAtAttribute::make(),
            Attributes\DeletedAtAttribute::make(),
        ];
    }
}
