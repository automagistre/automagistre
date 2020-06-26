<?php

declare(strict_types=1);

namespace App\Order\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderPayment
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="payments")
     */
    private Order $order;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $money;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $description = null;

    public function __construct(Order $order, Money $money, ?string $description)
    {
        $this->id = Uuid::uuid6();
        $this->order = $order;
        $this->money = $money;
        $this->description = $description;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
