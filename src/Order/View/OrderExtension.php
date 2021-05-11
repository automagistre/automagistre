<?php

declare(strict_types=1);

namespace App\Order\View;

use App\Calendar\Entity\EntryView;
use App\Car\Entity\Car;
use App\Car\Entity\Recommendation;
use App\Customer\Entity\Operand;
use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Order\Entity\OrderItemService;
use App\Shared\Doctrine\Registry;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderExtension extends AbstractExtension
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'recommendation_by_service',
                fn (OrderItemService $service) => $this->registry
                    ->repository(Recommendation::class)
                    ->findOneBy([
                        'realization' => $service->toId(),
                    ], ['id' => 'DESC']),
            ),
            new TwigFunction(
                'entry_by_order',
                fn (OrderId $orderId) => $this->registry->findOneBy(EntryView::class, ['orderId' => $orderId]),
            ),
            new TwigFunction(
                'order_info',
                function (Environment $twig, Order $order, bool $statusSelector = false): string {
                    return $twig->render('easy_admin/order/includes/main_information.html.twig', [
                        'order' => $order,
                        'status_selector' => $statusSelector,
                        'car' => $this->registry->findOneBy(Car::class, ['id' => $order->getCarId()]),
                        'customer' => $this->registry->findOneBy(Operand::class, ['id' => $order->getCustomerId()]),
                        'calendarEntry' => $this->registry->findOneBy(EntryView::class, ['orderId' => $order->toId()]),
                    ]);
                },
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ],
            ),
        ];
    }
}
