<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Customer\Entity\OperandId;
use App\Order\Entity\OrderId;
use App\Wallet\Entity\WalletId;
use Money\Money;

/**
 * @psalm-immutable
 */
final class CreatePayment
{
    public function __construct(
        public OrderId $orderId,
        public ?OperandId $customerId,
        public WalletId $walletId,
        public Money $money,
    ) {
    }
}
