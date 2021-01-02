<?php

declare(strict_types=1);

namespace App\Review\GraphQL\Type;

use App\GraphQL\Context;
use App\GraphQL\Type\Types;
use App\Review\Entity\Review;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ObjectType;

final class ReviewType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => Types::nonNull(Types::id()),
                'author' => Types::string(),
                'content' => [
                    'type' => Types::string(),
                    'resolve' => static function (Review $rootValue): string {
                        return $rootValue->text;
                    },
                    'deprecationReason' => 'Renamed to `text`.',
                ],
                'text' => Types::nonNull(Types::string()),
                'source' => [
                    'type' => Types::reviewSource(),
                    'resolve' => static function (Review $rootValue): int {
                        return $rootValue->source->toId();
                    },
                ],
                'rating' => [
                    'type' => Types::nonNull(Types::reviewRating()),
                    'resolve' => static function (Review $rootValue): int {
                        return $rootValue->rating->toId();
                    },
                ],
                'publishAt' => Types::date(),
            ],
            'args' => [
                'id' => Types::nonNull(Types::uuid()),
            ],
            'resolve' => function ($rootValue, array $args, Context $context): Deferred {
                return $context->buffer->add(Review::class, $args['id']);
            },
            'interfaces' => fn (): array => [
                Types::node(),
            ],
        ];

        parent::__construct($config);
    }
}
