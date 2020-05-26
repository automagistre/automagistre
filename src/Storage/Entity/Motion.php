<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Income\Entity\IncomePartId;
use App\Order\Entity\OrderId;
use App\Part\Entity\PartId;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Shared\Identifier\Identifier;
use App\Storage\Enum\Source;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(columns={"part_id", "created_at"}),
 *     }
 * )
 */
class Motion
{
    use Identity;
    use CreatedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="part_id")
     */
    private PartId $partId;

    /**
     * @ORM\Column(type="motion_source_enum")
     */
    private Source $source;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $sourceId;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    public function __construct(
        PartId $partId,
        int $quantity,
        Source $source,
        UuidInterface $sourceId,
        string $description = null
    ) {
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
    }

    public function getPartId(): PartId
    {
        return $this->partId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getSourceId(): UuidInterface
    {
        return $this->sourceId;
    }

    public function getSourceAsIdentifier(): Identifier
    {
        if ($this->source->isIncome()) {
            return IncomePartId::fromUuid($this->sourceId);
        }

        if ($this->source->isOrder()) {
            return OrderId::fromUuid($this->sourceId);
        }

        throw new LogicException('Not implemented');
    }
}
