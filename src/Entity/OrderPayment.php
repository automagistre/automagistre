<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderPayment
{
    use Identity;
    use CreatedAt;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="payments")
     */
    private $order;

    /**
     * @var Payment
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Payment")
     */
    private $payment;

    public function __construct(Order $order, Payment $payment)
    {
        $this->order = $order;
        $this->payment = $payment;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
