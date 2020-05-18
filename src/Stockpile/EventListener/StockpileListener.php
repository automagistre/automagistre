<?php

declare(strict_types=1);

namespace App\Stockpile\EventListener;

use App\Order\Entity\Order;
use App\Order\Entity\OrderItemPart;
use App\Order\Event\OrderClosed;
use App\Part\Domain\Part;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartDecreased;
use App\Part\Manager\PartManager;
use App\State;
use App\Stockpile\Manager\StockpileManager;
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
