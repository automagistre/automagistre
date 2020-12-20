<?php

declare(strict_types=1);

namespace App\Part\GraphQL\Type;

use App\Part\Enum\Unit;
use GraphQL\Type\Definition\EnumType;

final class UnitType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (Unit::all() as $enum) {
            $values[$enum->toName()] = [
                'value' => $enum->toId(),
                'description' => $enum->toLabel(),
            ];
        }

        $config = [
            'values' => $values,
        ];

        parent::__construct($config);
    }
}
