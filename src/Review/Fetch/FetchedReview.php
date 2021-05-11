<?php

declare(strict_types=1);

namespace App\Review\Fetch;

use App\Review\Entity\Review;
use App\Review\Entity\ReviewId;
use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final class FetchedReview
{
    public function __construct(
        public string $sourceId,
        public ReviewSource $source,
        public string $author,
        public string $text,
        public ReviewRating $rating,
        public DateTimeImmutable $publishAt,
        public array $raw,
    ) {
    }

    public function toReview(ReviewId $reviewId): Review
    {
        return new Review(
            $reviewId,
            $this->sourceId,
            $this->source,
            $this->author,
            $this->text,
            $this->rating,
            $this->publishAt,
            $this->raw,
        );
    }
}
