<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\Vehicle\Enum\DriveWheelConfiguration;
use GraphQL\Type\Definition\EnumType;

final class WheelDriveType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (DriveWheelConfiguration::all() as $enum) {
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
