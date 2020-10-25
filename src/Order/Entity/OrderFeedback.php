<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Order\Enum\OrderSatisfaction;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderFeedback
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="order_id")
     */
    public OrderId $orderId;

    /**
     * @ORM\Column(type="order_satisfaction_enum")
     */
    public OrderSatisfaction $satisfaction;

    public function __construct(UuidInterface $id, OrderId $orderId, OrderSatisfaction $satisfaction)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->satisfaction = $satisfaction;
    }

    public static function create(OrderId $orderId, OrderSatisfaction $satisfaction): self
    {
        return new self(
            Uuid::uuid6(),
            $orderId,
            $satisfaction,
        );
    }
}
