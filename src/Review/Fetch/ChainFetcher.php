<?php

declare(strict_types=1);

namespace App\Review\Fetch;

final class ChainFetcher implements Fetcher
{
    private array $fetchers;

    public function __construct(Fetcher ...$fetchers)
    {
        $this->fetchers = $fetchers;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(): iterable
    {
        foreach ($this->fetchers as $fetcher) {
            yield from $fetcher->fetch();
        }
    }
}
