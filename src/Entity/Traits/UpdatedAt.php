<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait UpdatedAt
{
    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
