<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\ObjectType;

final class ProductionType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'from' => Types::year(),
                'till' => Types::year(),
            ],
        ];

        parent::__construct($config);
    }
}
