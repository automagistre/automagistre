<?php

declare(strict_types=1);

namespace App\Google\Messages;

use Ramsey\Uuid\UuidInterface;

final class ReviewReceived
{
    public UuidInterface $reviewId;

    public function __construct(UuidInterface $reviewId)
    {
        $this->reviewId = $reviewId;
    }
}
