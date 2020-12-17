<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\Vehicle\Enum\Injection;
use GraphQL\Type\Definition\EnumType;

final class InjectionType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (Injection::all() as $enum) {
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
