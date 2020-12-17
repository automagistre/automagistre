<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use GraphQL\Type\Definition\EnumType;

final class FuelType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (\App\Vehicle\Enum\FuelType::all() as $enum) {
            $values[$enum->toCode()] = [
                'value' => $enum->toId(),
                'description' => $enum->toName(),
            ];
        }

        $config = [
            'values' => $values,
        ];

        parent::__construct($config);
    }
}
