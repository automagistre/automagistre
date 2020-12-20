<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\Vehicle\Enum\BodyType;
use GraphQL\Type\Definition\EnumType;

final class BodyEnumType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (BodyType::all() as $enum) {
            $values[$enum->toName()] = [
                'value' => $enum->toId(),
                'description' => $enum->toDisplayName(),
            ];
        }

        $config = [
            'values' => $values,
        ];

        parent::__construct($config);
    }
}
