<?php

declare(strict_types=1);

namespace App\Order\Form\Payment;

use App\Customer\Entity\OperandId;
use App\Order\Entity\OrderId;
use Money\Money;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrderPaymentDto
{
    public OrderId $orderId;

    /**
     * @var Money
     */
    public $payment;

    /**
     * @var OperandId|null
     */
    public $recipient;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var OrderPaymentWalletDto[]
     */
    public array $wallets = [];

    public function __construct(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }
}
