<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity
 */
class Supply
{
    use Identity;
    use CreatedAt;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Part")
     * @ORM\JoinColumn(nullable=false)
     */
    private $part;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receivedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $receivedAt;

    public function __construct(Operand $supplier, Part $part, Money $price, int $quantity)
    {
        $this->supplier = $supplier;
        $this->part = $part;
        $this->price = (int) $price->getAmount();
        $this->quantity = $quantity;
    }

    public function getSupplier(): Operand
    {
        return $this->supplier;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getPrice(): Money
    {
        return new Money($this->price, new Currency('RUB'));
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getReceivedBy(): ?User
    {
        return $this->receivedBy;
    }

    public function getReceivedAt(): ?\DateTime
    {
        return $this->receivedAt ? clone $this->receivedAt : null;
    }

    public function receive(User $user): void
    {
        $this->receivedBy = $user;
        $this->receivedAt = new \DateTime();
    }
}
