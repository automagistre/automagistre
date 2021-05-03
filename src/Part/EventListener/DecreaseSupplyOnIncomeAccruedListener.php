<?php

declare(strict_types=1);

namespace App\Part\EventListener;

use App\Income\Event\IncomeAccrued;
use App\Part\Entity\PartView;
use App\Part\Entity\Supply;
use App\Part\Enum\SupplySource;
use App\Shared\Doctrine\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DecreaseSupplyOnIncomeAccruedListener implements EventSubscriberInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            IncomeAccrued::class => 'onIncome',
        ];
    }

    public function onIncome(IncomeAccrued $event): void
    {
        $em = $this->registry->manager(Supply::class);

        $em->transactional(function (EntityManagerInterface $em) use ($event): void {
            $income = $event->getSubject();
            $supplier = $income->getSupplierId();

            foreach ($income->getIncomeParts() as $incomePart) {
                /** @var PartView $partView */
                $partView = $this->registry->get(PartView::class, $incomePart->partId);

                foreach ($partView->supplies() as $supply) {
                    if (!$supply->supplierId->equals($supplier)) {
                        continue;
                    }

                    $decrease = $incomePart->getQuantity();

                    if ($decrease > $supply->quantity) {
                        // Supply cannot be negative
                        $decrease = $supply->quantity;
                    }
                    $decrease = 0 - $decrease;

                    $em->persist(
                        new Supply(
                            $supply->partId,
                            $supply->supplierId,
                            $decrease,
                            SupplySource::income(),
                            $income->toId()->toUuid(),
                        )
                    );
                }
            }
        });
    }
}
