<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\Vehicle\Enum\AirIntake;
use GraphQL\Type\Definition\EnumType;

final class AirIntakeType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (AirIntake::all() as $enum) {
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
