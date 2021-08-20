<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Part\Entity\PartId;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use App\Tenant\Entity\TenantEntity;

/**
 * @ORM\Entity
 */
class IncomePart extends TenantEntity
{
    /**
     * @ORM\Column(type="part_id")
     */
    public PartId $partId;

    /**
     * @ORM\Id
     * @ORM\Column(type="income_part_id")
     */
    public IncomePartId $id;

    /**
     * @ORM\ManyToOne(targetEntity=Income::class, inversedBy="incomeParts")
     */
    public Income $income;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    public Money $price;

    /**
     * @ORM\Column(type="integer")
     */
    public int $quantity;

    public function __construct(IncomePartId $id, Income $income, PartId $partId, Money $price, int $quantity)
    {
        $this->id = $id;
        $this->income = $income;
        $this->partId = $partId;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function toId(): IncomePartId
    {
        return $this->id;
    }

    public function getTotalPrice(): Money
    {
        return $this->price->multiply((string) ($this->quantity / 100));
    }
}
