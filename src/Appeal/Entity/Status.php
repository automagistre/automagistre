<?php

declare(strict_types=1);

namespace App\Appeal\Entity;

use App\Appeal\Enum\AppealStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="appeal_status")
 *
 * @psalm-immutable
 */
class Status
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

    /**
     * @ORM\Column(type="appeal_status")
     */
    public AppealStatus $status;

    public function __construct(UuidInterface $id, AppealId $appealId, AppealStatus $status)
    {
        $this->id = $id;
        $this->appealId = $appealId;
        $this->status = $status;
    }

    public static function create(AppealId $appealId, AppealStatus $status): self
    {
        return new self(
            Uuid::uuid6(),
            $appealId,
            $status,
        );
    }
}
