<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Customer\Entity\OperandId;

/**
 * @psalm-immutable
 */
final class SupplyView
{
    public PartId $partId;

    public OperandId $supplierId;

    public int $quantity;

    public function __construct(PartId $partId, OperandId $supplierId, int $quantity)
    {
        $this->partId = $partId;
        $this->supplierId = $supplierId;
        $this->quantity = $quantity;
    }
}
