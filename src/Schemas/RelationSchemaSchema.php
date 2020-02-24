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
            \Amethyst\Core\Attributes\DataNameAttribute::make('data')
                ->setRequired(true),
            Attributes\EnumAttribute::make('type', config('amethyst.relation-schema.data.relation-schema.attributes.type.options'))
                ->setRequired(true),
            Attributes\YamlAttribute::make('payload')
                ->setRequired(true),
            Attributes\CreatedAtAttribute::make(),
            Attributes\UpdatedAtAttribute::make(),
        ];
    }
}
