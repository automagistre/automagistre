<?php

declare(strict_types=1);

namespace App\Yandex\Map\Messages;

use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderStorage;
use App\Order\Messages\OrderClosed;
use App\Sms\Messages\SendSms;
use DateTimeImmutable;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

final class SmsToRequestReviews implements MessageHandler
{
    private OrderStorage $orderStorage;

    private RouterInterface $router;

    private MessageBusInterface $messageBus;

    public function __construct(OrderStorage $orderStorage, RouterInterface $router, MessageBusInterface $messageBus)
    {
        $this->orderStorage = $orderStorage;
        $this->router = $router;
        $this->messageBus = $messageBus;
    }

    public function __invoke(OrderClosed $event): void
    {
        $order = $this->orderStorage->get($event->orderId);

        $customerId = $order->getCustomerId();
        if (null === $customerId) {
            return;
        }

        $orderClose = $order->getClose();
        if (!$orderClose->satisfaction->isGood()) {
            return;
        }

        $dateSend = new DateTimeImmutable('+1 hour');
        if ($dateSend > (new DateTimeImmutable())->setTime(20, 0, 0)) {
            $dateSend = (new DateTimeImmutable('+1 day'))->setTime(10, 0, 0);
        }

        $message = 'Благодарим за выбор нашего автосервиса. Помогите стать лучше, оставив отзыв о нашей работе ';
        $message .= $this->router->generate('yandex_map_url', [], RouterInterface::ABSOLUTE_URL);

        $this->messageBus->dispatch(
            new SendSms(
                $customerId,
                $message,
                [],
                $dateSend,
            )
        );
    }
}
