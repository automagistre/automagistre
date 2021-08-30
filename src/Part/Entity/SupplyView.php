<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Customer\Entity\OperandId;
use App\Tenant\Entity\TenantEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="supply_view")
 *
 * @psalm-immutable
 */
class SupplyView extends TenantEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="operand_id")
     */
    public OperandId $supplierId;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    public DateTimeImmutable $updatedAt;

    public function __construct(PartId $partId, OperandId $supplierId, int $quantity, DateTimeImmutable $updatedAt)
    {
        $this->partId = $partId;
        $this->supplierId = $supplierId;
        $this->quantity = $quantity;
        $this->updatedAt = $updatedAt;
    }
}
