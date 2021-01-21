<?php

declare(strict_types=1);

namespace App\Review\Event;

use App\Review\Entity\ReviewId;

/**
 * @psalm-immutable
 */
final class ReviewReceived
{
    public ReviewId $reviewId;

    public function __construct(ReviewId $reviewId)
    {
        $this->reviewId = $reviewId;
    }
}
