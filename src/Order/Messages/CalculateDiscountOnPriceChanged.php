<?php

declare(strict_types=1);

namespace App\Order\Messages;

use App\Doctrine\Registry;
use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Event\OrderItemPartCreated;
use App\Order\Event\OrderItemPartPriceChanged;
use App\Part\Entity\PartView;
use App\Part\Event\PartPriceChanged;

final class CalculateDiscountOnPriceChanged implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(OrderItemPartPriceChanged|OrderItemPartCreated|PartPriceChanged $event): void
    {
        $items = [];

        if ($event instanceof PartPriceChanged) {
            $items = $this->registry->manager()->createQueryBuilder()
                ->select('t')
                ->from(OrderItemPart::class, 't')
                ->join('t.order', 'o')
                ->leftJoin('o.close', 'close')
                ->where('close.id IS NULL')
                ->andWhere('t.partId = :partId')
                ->getQuery()
                ->setParameter('partId', $event->partId)
                ->getResult()
            ;
        } else {
            $item = $this->registry->find(OrderItemPart::class, $event->itemId);

            if (null !== $item) {
                $items = [$item];
            }
        }

        /** @var OrderItemPart[] $items */
        foreach ($items as $orderItemPart) {
            $partView = $this->registry->get(PartView::class, $orderItemPart->getPartId());

            $orderItemPart->changeDiscount($partView->price);
        }
    }
}
