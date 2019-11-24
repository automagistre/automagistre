<?php

declare(strict_types=1);

namespace App\EasyAdmin\Notify;

use App\Entity\Tenant\WalletTransaction;
use App\Event\PaymentCreated;
use App\Manager\PaymentManager;
use App\State;
use Money\MoneyFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BalanceNotificationListener implements EventSubscriberInterface
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var State
     */
    private $state;

    /**
     * @var PaymentManager
     */
    private $manager;

    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(
        PublisherInterface $publisher,
        State $state,
        PaymentManager $manager,
        MoneyFormatter $formatter
    ) {
        $this->publisher = $publisher;
        $this->state = $state;
        $this->manager = $manager;
        $this->formatter = $formatter;
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

    public function onPaymentCreated(PaymentCreated $event): void
    {
        $payment = $event->getSubject();
        if ($payment instanceof WalletTransaction) {
            $wallet = $payment->getRecipient();

            $topics = [
                "http://automagistre.ru/{$this->state->tenant()->getIdentifier()}/Wallet/{$wallet->getId()}",
            ];

            $data = [
                'id' => $wallet->getId(),
                'amount' => $this->formatter->format($this->manager->balance($wallet)),
            ];

            ($this->publisher)(new Update($topics, \json_encode($data, JSON_THROW_ON_ERROR)));
        }
    }
}
