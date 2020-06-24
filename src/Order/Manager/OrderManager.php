<?php

declare(strict_types=1);

namespace App\Order\Manager;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\Operand;
use App\Customer\Enum\CustomerTransactionSource;
use App\Employee\Entity\Employee;
use App\Order\Entity\Order;
use App\Order\Entity\OrderContractor;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Payment\Manager\PaymentManager;
use App\Shared\Doctrine\Registry;
use App\State;
use App\Storage\Entity\Motion;
use App\Storage\Enum\Source;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderManager
{
    private Registry $registry;

    private State $state;

    private PaymentManager $paymentManager;

    private ReservationManager $reservationManager;

    public function __construct(
        Registry $registry,
        State $state,
        PaymentManager $paymentManager,
        ReservationManager $reservationManager
    ) {
        $this->registry = $registry;
        $this->state = $state;
        $this->paymentManager = $paymentManager;
        $this->reservationManager = $reservationManager;
    }

    public function close(Order $order): void
    {
        $em = $this->registry->manager(Order::class);

        $em->transactional(function (EntityManagerInterface $em) use ($order): void {
            $em->refresh($order);

            $user = $this->state->user();
            $customer = null === $order->getCustomerId()
                ? null
                : $this->registry->findBy(Operand::class, ['uuid' => $order->getCustomerId()]);
            $balance = $customer instanceof Operand ? $this->paymentManager->balance($customer) : null;

            $order->close($user, $balance);

            if ($customer instanceof Operand) {
                foreach ($order->getPayments() as $payment) {
                    $em->persist(
                        new CustomerTransaction(
                            CustomerTransactionId::generate(),
                            $customer->toId(),
                            $payment->getMoney(),
                            CustomerTransactionSource::orderPrepay(),
                            $order->toId()->toUuid(),
                            null
                        )
                    );
                }

                $em->persist(
                    new CustomerTransaction(
                        CustomerTransactionId::generate(),
                        $customer->toId(),
                        $order->getTotalPrice(true)->negative(),
                        CustomerTransactionSource::orderPayment(),
                        $order->toId()->toUuid(),
                        null
                    )
                );
            }

            foreach ($order->getItems(OrderItemPart::class) as $item) {
                /** @var OrderItemPart $item */
                $partId = $item->getPartId();
                $quantity = $item->getQuantity();

                if (0 !== $this->reservationManager->reserved($item)) {
                    $this->reservationManager->deReserve($item, $quantity);
                }

                $em->persist(new Motion($partId, 0 - $quantity, Source::order(), $order->toId()->toUuid()));
            }

            foreach ($order->getItems(OrderItemService::class) as $item) {
                /** @var OrderItemService $item */
                $price = $item->getTotalPrice(true);
                if (!$price->isPositive()) {
                    continue;
                }

                $worker = $item->workerId;
                if (null === $worker) {
                    continue;
                }

                /** @var Operand $worker */
                $worker = $this->registry->findBy(Operand::class, ['uuid' => $worker]);
                $employee = $em->getRepository(Employee::class)->findOneBy(['person.id' => $worker->getId()]);

                if (!$employee instanceof Employee) {
                    $em->persist(new OrderContractor($order->toId(), $worker->toId(), $price));

                    continue;
                }

                $salary = $price->multiply($employee->getRatio() / 100);

                $em->persist(
                    new CustomerTransaction(
                        CustomerTransactionId::generate(),
                        $worker->toId(),
                        $salary->absolute(),
                        CustomerTransactionSource::orderSalary(),
                        $order->toId()->toUuid(),
                        null
                    )
                );
            }
        });
    }
}
