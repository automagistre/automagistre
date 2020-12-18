<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="appeal_postpone")
 *
 * @psalm-immutable
 */
class Postpone
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="appeal_id")
     */
    public AppealId $appealId;

    public function __construct(UuidInterface $id, AppealId $appealId)
    {
        $this->id = $id;
        $this->appealId = $appealId;
    }

    public static function create(AppealId $appealId): self
    {
        return new self(
            Uuid::uuid6(),
            $appealId,
        );
    }
}
