<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Part\Entity\PartId;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class McPart
{
    use Identity;

    /**
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $uuid;

    /**
     * @ORM\ManyToOne(targetEntity=McLine::class, inversedBy="parts")
     */
    public ?McLine $line;

    /**
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $recommended;

    public function __construct(McLine $line, PartId $partId, int $quantity, bool $recommended)
    {
        $this->uuid = Uuid::uuid6();
        $this->line = $line;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }

    public function toId(): UuidInterface
    {
        return $this->uuid;
    }
}
