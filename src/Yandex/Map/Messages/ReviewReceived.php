<?php

declare(strict_types=1);

namespace App\Yandex\Map\Messages;

use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
final class ReviewReceived
{
    public UuidInterface $reviewId;

    public function __construct(UuidInterface $reviewId)
    {
        $this->reviewId = $reviewId;
    }
}
