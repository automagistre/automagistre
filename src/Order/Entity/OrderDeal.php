<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Keycloak\Entity\UserId;
use App\Order\Enum\OrderSatisfaction;
use App\Order\Messages\OrderDealed;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 */
class OrderDeal extends OrderClose
{
    /**
     * @ORM\Column(type="money")
     */
    public Money $balance;

    /**
     * @ORM\Column(type="order_satisfaction_enum")
     */
    public OrderSatisfaction $satisfaction;

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;


    public function __construct(Order $order, ?Money $balance, OrderSatisfaction $satisfaction)
    {
        parent::__construct($order);

        $this->balance = $balance ?? Money::RUB(0);
        $this->satisfaction = $satisfaction;

        $this->record(new OrderDealed($order->toId()));
    }
}
