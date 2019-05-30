<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Landlord\Balance;
use App\Entity\Tenant\OperandTransaction;
use App\Event\PaymentCreated;
use App\Manager\PaymentManager;
use App\State;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BalanceListener implements EventSubscriberInterface
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(State $state, Registry $registry, PaymentManager $paymentManager)
    {
        $this->state = $state;
        $this->registry = $registry;
        $this->paymentManager = $paymentManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PaymentCreated::class => 'onPaymentCreated',
        ];
    }

    public function onPaymentCreated(GenericEvent $event): void
    {
        $payment = $event->getSubject();
        if (!$payment instanceof OperandTransaction) {
            return;
        }
        $operand = $payment->getRecipient();

        $conn = $this->registry->manager(Balance::class)->getConnection();
        $money = $this->paymentManager->balance($operand);

        $conn->prepare('
            INSERT INTO balance (operand_id, tenant, price_amount, price_currency_code) 
            VALUES (:operand, :tenant, :price, :currency) 
            ON DUPLICATE KEY UPDATE price_amount = :price
        ')
            ->execute([
                'operand' => $operand->getId(),
                'tenant' => $this->state->tenant(),
                'price' => $money->getAmount(),
                'currency' => $money->getCurrency(),
            ]);
    }
}
