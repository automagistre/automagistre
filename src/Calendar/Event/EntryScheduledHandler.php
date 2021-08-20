<?php

declare(strict_types=1);

namespace App\Calendar\Event;

use App\Calendar\Entity\EntryView;
use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Order\Entity\Order;
use App\Sms\Enum\Feature;
use App\Sms\Messages\SendSms;
use App\Tenant\State;
use DateTimeImmutable;
use Money\MoneyFormatter;
use Symfony\Component\Messenger\MessageBusInterface;
use function sprintf;
use function str_replace;

final class EntryScheduledHandler implements MessageHandler
{
    public function __construct(
        private Registry $registry,
        private MessageBusInterface $commandBus,
        private MoneyFormatter $formatter,
        private State $state,
    ) {
    }

    public function __invoke(EntryScheduled $event): void
    {
        /** @var EntryView $entry */
        $entry = $this->registry->getBy(EntryView::class, ['id' => $event->id]);

        if (null === $entry->orderInfo->customerId) {
            return;
        }

        if ($entry->schedule->date < new DateTimeImmutable()) {
            return;
        }

        $date = $entry->schedule->date;

        $message = '';

        if ($date->format('Y-m-d') === (new DateTimeImmutable())->format('Y-m-d')) {
            $message = 'Сегодня ';
        } elseif ($date->format('Y-m-d') === (new DateTimeImmutable('+1 day'))->format('Y-m-d')) {
            $message = 'Завтра ';
        }

        $message .= $date->format('d.m в H:i');

        $message = str_replace(
            [
                '{date}',
            ],
            [
                $message,
            ],
            $this->state->get()->toSmsOnScheduledEntry(),
        );

        if (null !== $entry->orderId) {
            /** @var Order $order */
            $order = $this->registry->getBy(Order::class, $entry->orderId);
            $price = $order->getTotalPrice(true);

            if ($price->isPositive()) {
                $message .= sprintf(' Предварительная стоимость заказа: %s', $this->formatter->format($price));
            }
        }

        $this->commandBus->dispatch(
            new SendSms(
                $entry->orderInfo->customerId,
                $message,
                [
                    Feature::onceADay(),
                ],
            ),
        );
    }
}
