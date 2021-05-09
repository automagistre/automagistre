<?php

declare(strict_types=1);

namespace App\Appeal\Event;

use App\Appeal\Entity\AppealId;

/**
 * @psalm-immutable
 */
final class AppealCreated
{
    public function __construct(public AppealId $appealId)
    {
    }
}
