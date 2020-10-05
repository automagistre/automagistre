<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Customer\Entity\OperandId;
use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final class SupplyView
{
    public PartId $partId;

    public OperandId $supplierId;

    public int $quantity;

    public DateTimeImmutable $updatedAt;

    public function __construct(PartId $partId, OperandId $supplierId, int $quantity, DateTimeImmutable $updatedAt)
    {
        $this->partId = $partId;
        $this->supplierId = $supplierId;
        $this->quantity = $quantity;
        $this->updatedAt = $updatedAt;
    }
}
