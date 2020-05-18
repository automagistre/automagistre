<?php

declare(strict_types=1);

namespace App\Order\Entity;

use App\Entity\Tenant\OperandTransaction;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderSalary
{
    use Identity;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity=Order::class)
     */
    private $order;

    /**
     * @var OperandTransaction
     *
     * @ORM\ManyToOne(targetEntity=OperandTransaction::class)
     */
    private $transaction;

    public function __construct(Order $order, OperandTransaction $transaction)
    {
        $this->order = $order;
        $this->transaction = $transaction;
    }
}
