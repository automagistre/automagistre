<?php

declare(strict_types=1);

namespace App\Order\Form\Close;

use App\Car\Entity\CarId;
use App\Order\Entity\OrderId;
use App\Order\Form\Finish\OrderFinishDto;
use App\Order\Form\Payment\OrderPaymentDto;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderCloseDto
{
    public OrderId $orderId;

    /**
     * @Assert\Valid()
     */
    public OrderFinishDto $finish;

    /**
     * @Assert\Valid()
     */
    public OrderPaymentDto $payment;

    public function __construct(OrderId $orderId, ?CarId $carId)
    {
        $this->orderId = $orderId;
        $this->finish = new OrderFinishDto($orderId, $carId);
        $this->payment = new OrderPaymentDto($orderId);
    }
}
