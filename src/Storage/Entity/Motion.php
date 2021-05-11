<?php

declare(strict_types=1);

namespace App\Storage\Entity;

use App\MessageBus\ContainsRecordedMessages;
use App\MessageBus\PrivateMessageRecorderCapabilities;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartDecreased;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Motion implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     *
     * @psalm-readonly
     */
    public UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Part::class, inversedBy="motions")
     */
    private Part $part;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Embedded(class=MotionSource::class)
     */
    private MotionSource $source;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private ?string $description;

    public function __construct(
        Part $part,
        int $quantity,
        MotionSource $source,
        string $description = null,
    ) {
        $this->id = Uuid::uuid6();
        $this->part = $part;
        $this->quantity = $quantity;
        $this->source = $source;
        $this->description = $description;

        $this->record(
            $quantity > 0
                ? new PartAccrued($this->part->toId())
                : new PartDecreased($this->part->toId()),
        );
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSource(): MotionSource
    {
        return $this->source;
    }
}
