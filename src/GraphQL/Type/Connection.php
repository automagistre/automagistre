<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

/**
 * @psalm-immutable
 */
final class Connection
{
    public array $nodes;

    public PageInfo $pageInfo;

    public int $totalCount;

    public function __construct(array $nodes, PageInfo $pageInfo, int $totalCount)
    {
        $this->nodes = $nodes;
        $this->pageInfo = $pageInfo;
        $this->totalCount = $totalCount;
    }
}
