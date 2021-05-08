<?php

declare(strict_types=1);

namespace App\CreatedBy\Entity;

use App\User\Entity\UserView;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="created_by_view")
 *
 * @psalm-suppress MissingConstructor
 */
class CreatedByView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="user_view")
     */
    public UserView $by;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $at;

    public function __construct(UuidInterface $id, UserView $user, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->by = $user;
        $this->at = $createdAt;
    }
}
