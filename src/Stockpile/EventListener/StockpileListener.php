<?php

declare(strict_types=1);

namespace App\Stockpile\EventListener;

use App\Order\Entity\OrderItemPart;
use App\Order\Event\OrderClosed;
use App\Part\Domain\PartId;
use App\Part\Event\PartAccrued;
use App\Part\Event\PartDecreased;
use App\Part\Manager\PartManager;
use App\State;
use App\Stockpile\Manager\StockpileManager;
use function array_values;
use function count;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            PartAccrued::class => 'onPartAccrued',
            PartDecreased::class => 'onPartDecreased',
            OrderClosed::class => 'onPartOrderClose',
        ];
    }

    public function onPartAccrued(PartAccrued $event): void
    {
        $this->actualize($event->getSubject());
    }

    public function onPartDecreased(PartDecreased $event): void
    {
        $this->actualize($event->getSubject());
    }

    public function actualize(PartId $partId): void
    {
        $this->stockpileManager->actualize([
            [$partId, $this->state->tenant(), $this->partManager->inStock($partId)],
        ]);
    }

    public function onPartOrderClose(OrderClosed $event): void
    {
        $order = $event->getSubject();

        $values = [];
        foreach ($order->getItems(OrderItemPart::class) as $item) {
            /** @var OrderItemPart $item */
            $partId = $item->getPartId();

            $values[$partId->toString()] = [$partId, $this->state->tenant(), $this->partManager->inStock($partId)];
        }

        if (0 === count($values)) {
            return;
        }

        $this->stockpileManager->actualize(array_values($values));
    }
}
