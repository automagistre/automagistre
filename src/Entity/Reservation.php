<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Reservation
{
    use Identity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @var OrderItemPart
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderItemPart")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderItemPart;

    public function __construct(OrderItemPart $orderItemPart, int $quantity)
    {
        $this->orderItemPart = $orderItemPart;
        $this->quantity = $quantity;
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
