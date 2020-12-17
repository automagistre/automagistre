<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

/**
 * @psalm-immutable
 */
final class PageInfo
{
    public bool $hasNextPage;

    public bool $hasPreviousPage;

    public ?string $endCursor;

    public ?string $startCursor;

    public function __construct(bool $hasNextPage, bool $hasPreviousPage, ?string $endCursor, ?string $startCursor)
    {
        $this->hasNextPage = $hasNextPage;
        $this->hasPreviousPage = $hasPreviousPage;
        $this->endCursor = $endCursor;
        $this->startCursor = $startCursor;
    }
}
