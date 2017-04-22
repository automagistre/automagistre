<?php

declare(strict_types=1);

namespace App\Entity;

use App\Uuid\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class Supply
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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
        $this->id = UuidGenerator::generate();
        $this->supplier = $supplier;
        $this->part = $part;
        $this->price = (int) $price->getAmount();
        $this->quantity = $quantity;
        $this->createdAt = new \DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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

    public function getCreatedAt(): \DateTime
    {
        return clone $this->createdAt;
    }

    public function getReceivedBy(): ?User
    {
        return $this->receivedBy;
    }

    public function getReceivedAt(): ?\DateTime
    {
        return $this->receivedAt ? clone $this->receivedAt : null;
    }

    public function receive(User $user)
    {
        $this->receivedBy = $user;
        $this->receivedAt = new \DateTime();
    }
}
