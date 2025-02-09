<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Keycloak\Entity\UserId;
use App\Order\Messages\OrderCancelled;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 */
class OrderCancel extends OrderClose
{
    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(Order $order)
    {
        parent::__construct($order);

        $this->record(new OrderCancelled($order->toId()));
    }
}
