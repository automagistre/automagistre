<?php

declare(strict_types=1);

namespace App\GraphQL\Www;

use App\GraphQL\Type\Types;
use GraphQL\Type\Definition\Type;

final class Schema
{
    public static function create(): \GraphQL\Type\Schema
    {
        $queryType = new QueryType();

        return new \GraphQL\Type\Schema([
            'query' => $queryType,
            'typeLoader' => function (string $name) use ($queryType): Type {
                if ('Query' === $name) {
                    return $queryType;
                }

                /** @phpstan-ignore-next-line */
                return Types::{$name}();
            },
        ]);
    }
}
