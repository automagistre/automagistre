<?php

declare(strict_types=1);

namespace App\Appeal\Event;

use App\Appeal\Entity\AppealId;

/**
 * @psalm-immutable
 */
final class AppealCreated
{
    public AppealId $appealId;

    public function __construct(AppealId $appealId)
    {
        $this->appealId = $appealId;
    }
}
