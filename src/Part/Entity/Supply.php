<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Customer\Entity\OperandId;
use App\Part\Enum\SupplySource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="part_supply")
 */
class Supply
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="part_id")
     */
    private PartId $partId;

    /**
     * @ORM\Column(type="operand_id")
     */
    private OperandId $supplierId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="part_supply_source_enum")
     */
    private SupplySource $source;

    /**
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $sourceId;

    public function __construct(
        PartId $partId,
        OperandId $supplierId,
        int $quantity,
        SupplySource $source,
        UuidInterface $sourceId,
    ) {
        $this->id = Uuid::uuid6();
        $this->partId = $partId;
        $this->supplierId = $supplierId;
        $this->quantity = $quantity;
        $this->source = $source;
        $this->sourceId = $sourceId;
    }
}
