<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\Vehicle\Enum\Transmission;
use GraphQL\Type\Definition\EnumType;

final class TransmissionType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (Transmission::all() as $enum) {
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
