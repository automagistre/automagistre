<?php

declare(strict_types=1);

namespace App\Order\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var OrderItemPart
     *
     * @ORM\ManyToOne(targetEntity=OrderItemPart::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderItemPart;

    public function __construct(OrderItemPart $orderItemPart, int $quantity)
    {
        $this->id = Uuid::uuid6();
        $this->orderItemPart = $orderItemPart;
        $this->quantity = $quantity;
    }

    public function toId(): UuidInterface
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getOrderItemPart(): OrderItemPart
    {
        return $this->orderItemPart;
    }
}
