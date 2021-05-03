<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\Income\Entity\IncomePartId;
use App\Order\Entity\OrderId;
use App\Part\Entity\PartId;
use Premier\Identifier\Identifier;
use App\Storage\Enum\Source;
use App\User\Entity\UserId;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Motion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

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
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->quantity = $quantity;
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->description = $description;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
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
            return IncomePartId::from($this->sourceId);
        }

        if ($this->source->isOrder()) {
            return OrderId::from($this->sourceId);
        }

        if ($this->source->isManual()) {
            return UserId::from($this->sourceId);
        }

        throw new LogicException('Not implemented');
    }
}
