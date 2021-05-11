<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

/**
 * @psalm-immutable
 */
final class Connection
{
    public function __construct(
        public array $nodes,
        public PageInfo $pageInfo,
        public int $totalCount,
    ) {
    }
}
