<?php

declare(strict_types=1);

namespace App\Part\EventListener;

use App\Income\Entity\Income;
use App\Income\Event\IncomeAccrued;
use App\MessageBus\MessageHandler;
use App\Part\Entity\PartView;
use App\Part\Entity\Supply;
use App\Part\Enum\SupplySource;
use App\Shared\Doctrine\Registry;

final class DecreaseSupplyOnIncomeAccruedListener implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(IncomeAccrued $event): void
    {
        $income = $this->registry->get(Income::class, $event->incomeId);

        $em = $this->registry->manager(Supply::class);

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
                    ),
                );
            }
        }
    }
}
