<?php

declare(strict_types=1);

namespace App\MC\GraphQL\Type;

use App\GraphQL\Type\Types;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McLine;
use App\Site\Context;
use App\Vehicle\Entity\Embedded\Engine;
use App\Vehicle\Entity\Model;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;

final class MaintenanceType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => Types::nonNull(Types::id()),
                'engine' => [
                    'type' => Types::engine(),
                    'resolve' => static function (McEquipment $rootValue): Engine {
                        return $rootValue->equipment->engine;
                    },
                ],
                'transmission' => [
                    'type' => Types::transmission(),
                    'resolve' => static function (McEquipment $rootValue): int {
                        return $rootValue->equipment->transmission->toId();
                    },
                ],
                'vehicle' => [
                    'type' => Types::vehicle(),
                    'resolve' => static function (McEquipment $rootValue, array $args, Context $context): Deferred {
                        return $context->buffer->add(Model::class, $rootValue->vehicleId->toString());
                    },
                ],
                'wheelDrive' => [
                    'type' => Types::wheelDrive(),
                    'resolve' => static function (McEquipment $rootValue): int {
                        return $rootValue->equipment->wheelDrive->toId();
                    },
                ],
                'works' => [
                    'type' => Types::listOf(Types::work()),
                    'resolve' => static function (McEquipment $rootValue, array $args, Context $context): array {
                        return $context->registry->repository(McLine::class)->findBy(['equipment' => $rootValue]);
                    },
                ],
            ],
            'interfaces' => fn (): array => [
                Types::node(),
            ],
        ];

        parent::__construct($config);
    }
}
