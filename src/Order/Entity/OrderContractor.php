<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Customer\Entity\Operand;
use App\Entity\Embeddable\OperandRelation;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class OrderContractor
{
    use Identity;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity=Order::class)
     */
    private $order;

    /**
     * @var OperandRelation
     *
     * @ORM\Embedded(class=OperandRelation::class)
     */
    private $contractor;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class)
     */
    private $money;

    public function __construct(Order $order, Operand $contractor, Money $money)
    {
        $this->order = $order;
        $this->contractor = new OperandRelation($contractor);
        $this->money = $money;
    }
}
