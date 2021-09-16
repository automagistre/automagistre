<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\Keycloak\Entity\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 *
 * @psalm-suppress MissingConstructor
 */
class Blamable
{
    /**
     * @ORM\Column
     */
    public UserId $by;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $at;
}
