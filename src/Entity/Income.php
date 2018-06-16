<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class Income
{
    use Identity;
    use CreatedAt;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     */
    private $supplier;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\IncomePart", mappedBy="income",
     * orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $incomeParts;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    public function __construct(Operand $supplier, array $incomeParts, User $createdBy)
    {
        $this->supplier = $supplier;
        $this->createdBy = $createdBy;

        $this->incomeParts = new ArrayCollection();
        foreach ($incomeParts as [$part, $price, $quantity]) {
            $this->incomeParts[] = new IncomePart($this, $part, $price, $quantity);
        }
    }

    public function getSupplier(): Operand
    {
        return $this->supplier;
    }

    public function setSupplier(Operand $supplier): void
    {
        $this->supplier = $supplier;
    }

    /**
     * @return IncomePart[]
     */
    public function getIncomeParts(): array
    {
        return $this->incomeParts->toArray();
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function itemsCount(): int
    {
        return $this->incomeParts->count();
    }

    public function getTotalPrice(): Money
    {
        $money = null;
        foreach ($this->getIncomeParts() as $incomePart) {
            $price = $incomePart->getPrice();

            $money = $money instanceof Money ? $money->add($price) : $price;
        }

        return $money;
    }
}
