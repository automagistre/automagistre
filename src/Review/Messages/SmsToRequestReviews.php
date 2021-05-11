<?php

declare(strict_types=1);

namespace App\Review\Messages;

use App\Customer\Entity\CustomerStorage;
use App\Customer\Entity\Organization;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderDeal;
use App\Order\Entity\OrderStorage;
use App\Order\Messages\OrderDealed;
use App\Sms\Messages\SendSms;
use DateTimeImmutable;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use function assert;

final class SmsToRequestReviews implements MessageHandler
{
    public function __construct(
        private OrderStorage $orderStorage,
        private CustomerStorage $customerStorage,
        private RouterInterface $router,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(OrderDealed $event): void
    {
        $order = $this->orderStorage->get($event->orderId);

        $customerId = $order->getCustomerId();

        if (null === $customerId) {
            return;
        }

        $customer = $this->customerStorage->get($customerId);

        if ($customer instanceof Organization) {
            return;
        }

        $orderClose = $order->getClose();
        assert($orderClose instanceof OrderDeal);

        if (!$orderClose->satisfaction->isGood()) {
            return;
        }

        $dateSend = new DateTimeImmutable('+1 hour');

        if ($dateSend > (new DateTimeImmutable())->setTime(17, 0, 0)) {
            $dateSend = (new DateTimeImmutable('+1 day'))->setTime(7, 0, 0);
        }

        $message = 'Благодарим за выбор нашего автосервиса. Помогите стать лучше, оставив отзыв о нашей работе ';
        $message .= $this->router->generate('yandex_map_url', [], RouterInterface::ABSOLUTE_URL);

        $this->messageBus->dispatch(
            new SendSms(
                $customerId,
                $message,
                [],
                $dateSend,
            ),
        );
    }
}
