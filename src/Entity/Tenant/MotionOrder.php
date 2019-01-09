<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Landlord\Part;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MotionOrder extends Motion
{
    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Order")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    public function __construct(Part $part, int $quantity, Order $order)
    {
        if (0 < $quantity) {
            $quantity = 0 - $quantity;
        }

        parent::__construct($part, $quantity, \sprintf('Заказ #%s', $order->getId()));

        $this->order = $order;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }
}
