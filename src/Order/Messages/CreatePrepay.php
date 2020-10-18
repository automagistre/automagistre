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
final class CreatePrepay
{
    public OrderId $orderId;

    public ?OperandId $customerId;

    public WalletId $walletId;

    public Money $money;

    public ?string $description;

    public function __construct(
        OrderId $orderId,
        ?OperandId $customerId,
        WalletId $walletId,
        Money $money,
        ?string $description
    ) {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->walletId = $walletId;
        $this->money = $money;
        $this->description = $description;
    }
}
