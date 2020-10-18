<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Enum\CustomerTransactionSource;
use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Shared\Doctrine\Registry;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;

final class CreatePrepayHandler implements MessageHandler
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(CreatePrepay $command): void
    {
        $em = $this->registry->manager();

        $orderId = $command->orderId;

        /** @var Order $order */
        $order = $this->registry->get(Order::class, $orderId);
        $order->addPayment($command->money, $command->description);

        $em->persist(
            new WalletTransaction(
                WalletTransactionId::generate(),
                $command->walletId,
                $command->money,
                WalletTransactionSource::orderPrepay(),
                $orderId->toUuid(),
                $command->description,
            )
        );

        if (null !== $command->customerId) {
            $em->persist(
                new CustomerTransaction(
                    CustomerTransactionId::generate(),
                    $command->customerId,
                    $command->money,
                    CustomerTransactionSource::orderPrepay(),
                    $orderId->toUuid(),
                    $command->description,
                )
            );
        }
    }
}
