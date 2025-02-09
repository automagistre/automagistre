<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Part\Entity\PartId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 */
class McPart extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=McLine::class, inversedBy="parts")
     */
    public ?McLine $line;

    /**
     * @ORM\Column
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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(UuidInterface $id, McLine $line, PartId $partId, int $quantity, bool $recommended)
    {
        $this->id = $id;
        $this->line = $line;
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->recommended = $recommended;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }
}
