<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\User\Entity\UserView;
use DateTimeImmutable;

class CreatedByView
{
    public UserView $by;

    public DateTimeImmutable $at;

    public function __construct(UserView $user, DateTimeImmutable $createdAt)
    {
        $this->by = $user;
        $this->at = $createdAt;
    }
}
