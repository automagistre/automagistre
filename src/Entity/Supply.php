<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Money\Money;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"supplier", "part", "receivedAt"}, ignoreNull=false)
 */
class Supply
{
    use Identity;
    use CreatedAt;
    use Price;

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
    private $quantity;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $receivedBy;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $receivedAt;

    public function __construct(Operand $supplier, Part $part, Money $price, int $quantity)
    {
        $this->supplier = $supplier;
        $this->part = $part;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function updateFromModel(\App\Form\Model\Supply $supply): void
    {
        if (null !== $this->receivedAt) {
            throw new LogicException('Can\'t update received Supply.');
        }

        $this->price = $supply->price;
        $this->quantity = $supply->quantity;
    }

    public function getSupplier(): Operand
    {
        return $this->supplier;
    }

    public function getPart(): Part
    {
        return $this->part;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getReceivedBy(): ?User
    {
        return $this->receivedBy;
    }

    public function getReceivedAt(): ?DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function receive(User $user, int $quantity = null): void
    {
        if (null !== $quantity) {
            $this->quantity = $quantity;
        }

        $this->receivedBy = $user;
        $this->receivedAt = new DateTimeImmutable();
    }
}
