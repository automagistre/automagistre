<?php

declare(strict_types=1);

namespace App\Part\EventListener;

use App\Order\Entity\OrderItemPart;
use App\Part\Event\PartAccrued;
use App\Shared\Doctrine\Registry;
use App\Storage\Exception\ReservationException;
use App\Storage\Manager\ReservationManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReserveAccruedPartsListener implements EventSubscriberInterface
{
    private Registry $registry;

    private ReservationManager $reservationManager;

    public function __construct(Registry $registry, ReservationManager $reservationManager)
    {
        $this->registry = $registry;
        $this->reservationManager = $reservationManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PartAccrued::class => 'onPartAccrued',
        ];
    }

    public function onPartAccrued(PartAccrued $event): void
    {
        $partId = $event->getSubject();

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
            ->where('entity.part.part_id = :part')
            ->andWhere('orders.closedAt IS NULL')
            ->getQuery()
            ->setParameter('part', $partId)
            ->getResult();

        if ([] === $items) {
            return;
        }

        foreach ($items as $item) {
            if (0 >= $reservable) {
                break;
            }

            try {
                $this->reservationManager->reserve($item);
            } catch (ReservationException $e) {
                continue;
            }

            $reservable = $this->reservationManager->reservable($partId);
        }
    }
}
