<?php

namespace Amethyst\Schemas;

use Railken\Lem\Attributes;
use Railken\Lem\Contracts\EntityContract;
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
            \Amethyst\Core\Attributes\DataNameAttribute::make('data')
                ->setRequired(true)
                ->setMutable(false),
            Attributes\TextAttribute::make('name')
                ->setRequired(true)
                ->setValidator(function (EntityContract $entity, $value) {
                    return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $value);
                }),
            Attributes\LongTextAttribute::make('description'),
            Attributes\EnumAttribute::make('type', config('amethyst.relation-schema.data.relation-schema.attributes.type.options'))
                ->setRequired(true),
            Attributes\YamlAttribute::make('payload')
                ->setRequired(false),
            Attributes\CreatedAtAttribute::make(),
            Attributes\UpdatedAtAttribute::make(),
        ];
    }
}
