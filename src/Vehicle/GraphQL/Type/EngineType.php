<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\GraphQL\Type\Types;
use App\Vehicle\Entity\Embedded\Engine;
use GraphQL\Type\Definition\ObjectType;

final class EngineType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
                'name' => Types::string(),
                'type' => [
                    'type' => Types::fuel(),
                    'resolve' => static function (Engine $rootValue): int {
                        return $rootValue->type->toId();
                    },
                ],
                'airIntake' => [
                    'type' => Types::airIntake(),
                    'resolve' => static function (Engine $rootValue): int {
                        return $rootValue->airIntake->toId();
                    },
                ],
                'injection' => [
                    'type' => Types::injection(),
                    'resolve' => static function (Engine $rootValue): int {
                        return $rootValue->injection->toId();
                    },
                ],
                'capacity' => Types::string(),
            ],
        ];

        parent::__construct($config);
    }
}
