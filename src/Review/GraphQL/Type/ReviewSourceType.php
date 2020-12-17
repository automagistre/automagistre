<?php

declare(strict_types=1);

namespace App\Review\GraphQL\Type;

use GraphQL\Type\Definition\EnumType;

final class ReviewSourceType extends EnumType
{
    public function __construct()
    {
        $config = [
            'values' => [
                'club',
                'yandex',
                'google',
            ],
        ];

        parent::__construct($config);
    }
}
