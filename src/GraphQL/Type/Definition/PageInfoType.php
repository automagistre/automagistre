<?php

declare(strict_types=1);

namespace App\GraphQL\Type\Definition;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\ObjectType;

final class PageInfoType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'endCursor' => [
                    'type' => Types::string(),
                    'description' => 'When paginating forwards, the cursor to continue.',
                ],
                'hasNextPage' => [
                    'type' => Types::nonNull(Types::boolean()),
                    'description' => 'When paginating forwards, are there more items?.',
                ],
                /*
                'hasPreviousPage' => [
                    'type' => Types::nonNull(Types::boolean()),
                    'description' => 'When paginating backwards, are there more items?.',
                ],
                'startCursor' => [
                    'type' => Types::string(),
                    'description' => 'When paginating backwards, the cursor to continue.',
                ],
                */
            ],
        ];

        parent::__construct($config);
    }
}
