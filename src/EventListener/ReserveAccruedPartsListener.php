<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\OrderItemPart;
use App\Events;
use App\Manager\ReservationException;
use App\Manager\ReservationManager;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReserveAccruedPartsListener implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

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
            Events::PART_ACCRUED => 'onPartAccrued',
        ];
    }

    public function onPartAccrued(GenericEvent $event): void
    {
        $part = $event->getSubject();
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        $reservable = $this->reservationManager->reservable($part);
        if (0 >= $reservable) {
            return;
        }

        $em = $this->registry->manager(OrderItemPart::class);

        /** @var OrderItemPart[] $items */
        $items = $em->createQueryBuilder()
            ->select('entity', 'orders')
            ->from(OrderItemPart::class, 'entity')
            ->join('entity.order', 'orders')
            ->where('entity.part.id = :part')
            ->andWhere('orders.closedAt IS NULL')
            ->getQuery()
            ->setParameter('part', $part->getId())
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

            $reservable = $this->reservationManager->reservable($part);
        }
    }
}
