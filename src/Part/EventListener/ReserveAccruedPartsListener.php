<?php

declare(strict_types=1);

namespace App\Part\EventListener;

use App\MessageBus\MessageHandler;
use App\Order\Entity\OrderItemPart;
use App\Order\Exception\ReservationException;
use App\Order\Manager\ReservationManager;
use App\Part\Event\PartAccrued;
use App\Shared\Doctrine\Registry;

final class ReserveAccruedPartsListener implements MessageHandler
{
    public function __construct(
        private Registry $registry,
        private ReservationManager $reservationManager,
    ) {
    }

    public function __invoke(PartAccrued $event): void
    {
        $partId = $event->partId;

        $reservable = $this->reservationManager->reservable($partId);

        if (0 >= $reservable) {
            return;
        }

        $em = $this->registry->manager(OrderItemPart::class);

        /** @var OrderItemPart[] $items */
        $items = $em->createQueryBuilder()
            ->select(['entity', 'orders'])
            ->from(OrderItemPart::class, 'entity')
            ->join('entity.order', 'orders')
            ->leftJoin('orders.close', 'close')
            ->where('entity.partId = :part')
            ->andWhere('close.id IS NULL')
            ->getQuery()
            ->setParameter('part', $partId)
            ->getResult()
        ;

        if ([] === $items) {
            return;
        }

        foreach ($items as $item) {
            if (0 >= $reservable) {
                break;
            }

            try {
                $this->reservationManager->reserve($item);
            } catch (ReservationException) {
                continue;
            }

            $reservable = $this->reservationManager->reservable($partId);
        }
    }
}
