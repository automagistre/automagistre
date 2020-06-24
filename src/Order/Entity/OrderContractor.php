<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Customer\Entity\OperandId;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 */
class OrderContractor
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="order_id")
     */
    private OrderId $orderId;

    /**
     * @ORM\Column(type="operand_id")
     */
    private OperandId $operandId;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class)
     */
    private $money;

    public function __construct(OrderId $orderId, OperandId $operandId, Money $money)
    {
        $this->id = Uuid::uuid6();
        $this->orderId = $orderId;
        $this->operandId = $operandId;
        $this->money = $money;
    }
}
