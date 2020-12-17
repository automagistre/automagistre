<?php

declare(strict_types=1);

namespace App\MC\GraphQL\Type;

use App\GraphQL\Type\Types;
use App\GraphQL\Www\Context;
use App\MC\Entity\McPart;
use App\Part\Entity\PartView;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;

final class PartItemType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'part' => [
                    'type' => Types::nonNull(Types::part()),
                    'resolve' => static function (McPart $rootValue, array $args, Context $context): Deferred {
                        return $context->buffer->add(PartView::class, $rootValue->partId->toString());
                    },
                ],
                'quantity' => Types::nonNull(Types::int()),
                'recommended' => Types::nonNull(Types::boolean()),
            ],
        ];

        parent::__construct($config);
    }
}
