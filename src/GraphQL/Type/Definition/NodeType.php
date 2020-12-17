<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\InterfaceType;

final class NodeType extends InterfaceType
{
    public function __construct()
    {
        $config = [
            'description' => 'An object with an ID.',
            'fields' => fn (): array => [
                'id' => [
                    'type' => Types::nonNull(Types::id()),
                    'description' => 'ID of the object.',
                ],
            ],
        ];

        parent::__construct($config);
    }
}
