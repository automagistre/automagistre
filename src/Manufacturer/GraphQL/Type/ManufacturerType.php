<?php

declare(strict_types=1);

namespace App\Manufacturer\GraphQL\Type;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\ObjectType;

final class ManufacturerType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => Types::nonNull(Types::id()),
                'name' => Types::string(),
                'localizedName' => Types::string(),
            ],
            'interfaces' => [
                Types::node(),
            ],
        ];

        parent::__construct($config);
    }
}
