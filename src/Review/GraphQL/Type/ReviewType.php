<?php

declare(strict_types=1);

namespace App\Review\GraphQL\Type;

use App\GraphQL\Context;
use App\GraphQL\Type\Types;
use App\Review\Entity\ReviewView;
use GraphQL\Type\Definition\ObjectType;

final class ReviewType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => fn (): array => [
                'id' => Types::nonNull(Types::id()),
                'author' => Types::string(),
                'content' => Types::string(),
                'source' => Types::reviewSource(),
                'publishAt' => Types::date(),
            ],
            'args' => [
                'id' => Types::nonNull(Types::uuid()),
            ],
            'resolve' => function ($rootValue, array $args, Context $context): ReviewView {
                return $context->registry->get(ReviewView::class, $args['id']);
            },
            'interfaces' => fn (): array => [
                Types::node(),
            ],
        ];

        parent::__construct($config);
    }
}
