<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\IncomePart;
use App\Entity\Supply;
use App\Events;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplyReceiveListener implements EventSubscriberInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::INCOME_PART_ACCRUED => 'onIncomePartAccrued',
        ];
    }

    public function onIncomePartAccrued(GenericEvent $event): void
    {
        $em = $this->registry->getEntityManager();

        $incomePart = $event->getSubject();
        if (!$incomePart instanceof IncomePart) {
            throw new LogicException('IncomePart required.');
        }

        $income = $incomePart->getIncome();

        $supply = $incomePart->getSupply();
        if (!$supply instanceof Supply) {
            $supply = $em->getRepository(Supply::class)->findOneBy([
                'part' => $incomePart->getPart(),
                'supplier' => $income->getSupplier(),
                'receivedAt' => null,
            ]);
        }

        if ($supply instanceof Supply) {
            $difference = $supply->getQuantity() - $incomePart->getQuantity();

            $accruedBy = $income->getAccruedBy();
            if (0 >= $difference) {
                $supply->receive($accruedBy);
            } else {
                $em->persist(
                    new Supply($supply->getSupplier(), $supply->getPart(), $supply->getPrice(), $difference)
                );

                $supply->receive($accruedBy, $incomePart->getQuantity());
            }
        }

        $em->flush();
    }
}
