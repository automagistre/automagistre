<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Customer\Domain\Operand;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Embeddable\OperandRelation;
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
     * @ORM\Embedded(class="App\Entity\Embeddable\OperandRelation")
     */
    private $contractor;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="Money\Money")
     */
    private $money;

    public function __construct(Order $order, Operand $contractor, Money $money)
    {
        $this->order = $order;
        $this->contractor = new OperandRelation($contractor);
        $this->money = $money;
    }
}
