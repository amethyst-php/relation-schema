<?php

namespace Amethyst\Authorizers;

use Railken\Lem\Authorizer;
use Railken\Lem\Tokens;

class RelationSchemaAuthorizer extends Authorizer
{
    /**
     * List of all permissions.
     *
     * @var array
     */
    protected $permissions = [
        Tokens::PERMISSION_CREATE => 'relation-schema.create',
        Tokens::PERMISSION_UPDATE => 'relation-schema.update',
        Tokens::PERMISSION_SHOW   => 'relation-schema.show',
        Tokens::PERMISSION_REMOVE => 'relation-schema.remove',
    ];
}
