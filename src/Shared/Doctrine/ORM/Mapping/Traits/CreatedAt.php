<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Mapping\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedAt
{
    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updateCreatedAt(): void
    {
        if ($this->createdAt instanceof DateTimeImmutable) {
            throw new DomainException('Change defined createdAt is restricted');
        }

        $this->createdAt = new DateTimeImmutable();
    }
}
