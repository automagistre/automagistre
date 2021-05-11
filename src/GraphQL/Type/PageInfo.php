<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

/**
 * @psalm-immutable
 */
final class PageInfo
{
    public function __construct(
        public bool $hasNextPage,
        public bool $hasPreviousPage,
        public ?string $endCursor,
        public ?string $startCursor,
    ) {
    }
}
