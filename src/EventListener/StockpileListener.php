<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\OrderClosed;
use App\Event\PartAccrued;
use App\Event\PartDecreased;
use App\Manager\PartManager;
use App\Manager\StockpileManager;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Part\Domain\Part;
use App\State;
use function array_values;
use function count;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StockpileListener implements EventSubscriberInterface
{
    private PartManager $partManager;

    private StockpileManager $stockpileManager;

    private State $state;

    public function __construct(PartManager $partManager, StockpileManager $stockpileManager, State $state)
    {
        $this->partManager = $partManager;
        $this->stockpileManager = $stockpileManager;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PartAccrued::class => 'onPartMove',
            PartDecreased::class => 'onPartMove',
            OrderClosed::class => 'onPartOrderClose',
        ];
    }

    public function onPartMove(GenericEvent $event): void
    {
        $part = $event->getSubject();
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        $this->stockpileManager->actualize([
            [$part->getId(), $this->state->tenant(), $this->partManager->inStock($part)],
        ]);
    }

    public function onPartOrderClose(GenericEvent $event): void
    {
        $order = $event->getSubject();
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        $values = [];
        foreach ($order->getItems(OrderItemPart::class) as $item) {
            /** @var OrderItemPart $item */
            $part = $item->getPart();

            $id = $part->getId();
            $values[$id] = [$id, $this->state->tenant(), $this->partManager->inStock($part)];
        }

        if (0 === count($values)) {
            return;
        }

        $this->stockpileManager->actualize(array_values($values));
    }
}
