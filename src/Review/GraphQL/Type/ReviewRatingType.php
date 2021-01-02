<?php

declare(strict_types=1);

namespace App\Review\GraphQL\Type;

use App\Review\Enum\ReviewRating;
use GraphQL\Type\Definition\EnumType;

final class ReviewRatingType extends EnumType
{
    public function __construct()
    {
        $values = [];
        foreach (ReviewRating::all() as $enum) {
            $values[$enum->toName()] = [
                'value' => $enum->toId(),
            ];
        }

        $config = [
            'values' => $values,
        ];

        parent::__construct($config);
    }
}
