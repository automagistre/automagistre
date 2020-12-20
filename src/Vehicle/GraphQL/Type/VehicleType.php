<?php

declare(strict_types=1);

namespace App\Vehicle\GraphQL\Type;

use App\GraphQL\Type\Types;
use App\Manufacturer\Entity\Manufacturer;
use App\Site\Context;
use App\Vehicle\Entity\Model;
use GraphQL\Type\Definition\ObjectType;

final class VehicleType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => Types::nonNull(Types::id()),
                'name' => [
                    'type' => Types::nonNull(Types::string()),
                    'resolve' => static function (Model $rootValue, array $args, Context $context): string {
                        return $rootValue->name;
                    },
                ],
                'caseName' => Types::string(),
                'localizedName' => Types::string(),
                'manufacturer' => [
                    'type' => Types::nonNull(Types::manufacturer()),
                    'resolve' => static function (Model $rootValue, array $args, Context $context): Manufacturer {
                        return $context->registry->get(Manufacturer::class, $rootValue->manufacturerId);
                    },
                ],
                'production' => [
                    'type' => Types::production(),
                    'resolve' => static function (Model $rootValue): array {
                        return [
                            'from' => $rootValue->yearFrom,
                            'till' => $rootValue->yearTill,
                        ];
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
