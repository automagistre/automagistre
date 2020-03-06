<?php

declare(strict_types=1);

namespace App\EasyAdmin\Notify;

use App\Entity\Tenant\WalletTransaction;
use App\Event\PaymentCreated;
use App\State;
use App\Wallet\BalanceProvider;
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

    private MoneyFormatter $formatter;

    private BalanceProvider $balanceProvider;

    public function __construct(
        PublisherInterface $publisher,
        State $state,
        MoneyFormatter $formatter,
        BalanceProvider $balanceProvider
    ) {
        $this->publisher = $publisher;
        $this->state = $state;
        $this->formatter = $formatter;
        $this->balanceProvider = $balanceProvider;
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
                "http://automagistre.ru/{$this->state->tenant()->toIdentifier()}/Wallet/{$wallet->getId()}",
            ];

            $data = [
                'id' => $wallet->getId(),
                'amount' => $this->formatter->format($this->balanceProvider->balance($wallet)),
            ];

            ($this->publisher)(new Update($topics, json_encode($data, JSON_THROW_ON_ERROR)));
        }
    }
}
