<?php

declare(strict_types=1);

namespace App\Review\Fetch;

interface Fetcher
{
    /**
     * @return iterable<int, FetchedReview>
     */
    public function fetch(): iterable;
}
