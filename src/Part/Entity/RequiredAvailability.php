<?php

declare(strict_types=1);

namespace App\Part\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_required_availability")
 */
class RequiredAvailability
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="part_id")
     */
    private PartId $partId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $orderFromQuantity;

    /**
     * @ORM\Column(type="integer")
     */
    private int $orderUpToQuantity;

    public function __construct(PartId $partId, int $orderFromQuantity, int $orderUpToQuantity)
    {
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->orderFromQuantity = $orderFromQuantity;
        $this->orderUpToQuantity = $orderUpToQuantity;
    }
}
