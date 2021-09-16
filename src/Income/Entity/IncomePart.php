<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Part\Entity\PartId;
use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class IncomePart extends TenantEntity
{
    /**
     * @ORM\Column
     */
    public PartId $partId;

    /**
     * @ORM\Id
     * @ORM\Column
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
