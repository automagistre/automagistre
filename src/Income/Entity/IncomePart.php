<?php

declare(strict_types=1);

namespace App\Income\Entity;

use App\Part\Entity\PartId;
use App\Shared\Doctrine\ORM\Mapping\Traits\Price;
use App\Shared\Doctrine\ORM\Mapping\Traits\Quantity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class IncomePart
{
    use Price;
    use Quantity;

    /**
     * @ORM\Column(type="part_id")
     */
    public ?PartId $partId = null;

    /**
     * @ORM\Id
     * @ORM\Column(type="income_part_id")
     */
    private IncomePartId $id;

    /**
     * @ORM\ManyToOne(targetEntity=Income::class, inversedBy="incomeParts")
     */
    private Income $income;

    public function __construct(IncomePartId $id, Income $income)
    {
        $this->id = $id;
        $this->income = $income;
    }

    public function toId(): IncomePartId
    {
        return $this->id;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }

    public function getTotalPrice(): Money
    {
        return $this->getPrice()->multiply($this->quantity / 100);
    }

    public function getIncome(): Income
    {
        return $this->income;
    }
}
