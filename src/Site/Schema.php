<?php

declare(strict_types=1);

namespace App\Site;

final class Schema
{
    public static function create(): \GraphQL\Type\Schema
    {
        $queryType = new QueryType();
        $mutationType = new MutationType();

        return new \GraphQL\Type\Schema([
            'query' => $queryType,
            'mutation' => $mutationType,
            //            'typeLoader' => function (string $name) use ($queryType, $mutationType): ?Type {
            //                if ('Query' === $name) {
            //                    return $queryType;
            //                }
            //
            //                if ('Mutation' === $name) {
            //                    return $mutationType;
            //                }
            //
            //                /** @phpstan-ignore-next-line */
            //                return Types::{$name}();
            //            },
        ]);
    }
}
