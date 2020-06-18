<?php

declare(strict_types=1);

namespace App\EasyAdmin\Notify;

use App\Payment\Event\PaymentCreated;
use App\Payment\Manager\PaymentManager;
use App\State;
use App\Wallet\Entity\WalletTransaction;
use function json_encode;
use Money\MoneyFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BalanceNotificationListener implements EventSubscriberInterface
{
    private PublisherInterface $publisher;

    private State $state;

    private PaymentManager $manager;

    private MoneyFormatter $formatter;

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
            $wallet = $payment->getWallet();

            $topics = [
                "http://automagistre.ru/{$this->state->tenant()->toIdentifier()}/Wallet/{$wallet->toId()}",
            ];

            $data = [
                'id' => $wallet->toId(),
                'amount' => $this->formatter->format($this->manager->balance($wallet)),
            ];

            ($this->publisher)(new Update($topics, json_encode($data, JSON_THROW_ON_ERROR)));
        }
    }
}
