<?php

declare(strict_types=1);

namespace App\MC\GraphQL\Type;

use App\GraphQL\Type\Types;
use App\MC\Entity\McLine;
use App\MC\Entity\McPart;
use App\Site\Context;
use function array_map;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;
use Money\Money;

final class WorkType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => Types::nonNull(Types::id()),
                'name' => [
                    'type' => Types::nonNull(Types::string()),
                    'resolve' => static function (McLine $rootValue): string {
                        return $rootValue->work->name;
                    },
                ],
                'description' => [
                    'type' => Types::string(),
                    'resolve' => static function (McLine $rootValue): ?string {
                        return $rootValue->work->description;
                    },
                ],
                'parts' => [
                    'type' => Types::listOf(Types::partItem()),
                    'resolve' => static function (McLine $rootValue, array $args, Context $context): Deferred {
                        $ids = array_map(
                            static fn (McPart $mcPart): string => $mcPart->id->toString(),
                            $rootValue->parts->toArray(),
                        );

                        return $context->buffer->add(McPart::class, $ids);
                    },
                ],
                'period' => Types::nonNull(Types::int()),
                'price' => [
                    'type' => Types::nonNull(Types::money()),
                    'resolve' => static function (McLine $rootValue): Money {
                        return $rootValue->work->price;
                    },
                ],
                'recommended' => Types::nonNull(Types::boolean()),
                'position' => Types::nonNull(Types::int()),
            ],
            'interfaces' => [
                Types::node(),
            ],
        ];

        parent::__construct($config);
    }
}
