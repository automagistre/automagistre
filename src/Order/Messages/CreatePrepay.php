<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Order\Entity\OrderId;
use App\Wallet\Entity\WalletId;
use Money\Money;

/**
 * @psalm-immutable
 */
final class CreatePrepay
{
    public function __construct(
        public OrderId $orderId,
        public WalletId $walletId,
        public Money $money,
        public ?string $description,
    ) {
    }
}
