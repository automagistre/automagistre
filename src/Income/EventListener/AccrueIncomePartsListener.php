<?php

declare(strict_types=1);

namespace App\Income\EventListener;

use App\Income\Entity\Income;
use App\Income\Event\IncomeAccrued;
use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\MotionSource;
use App\Storage\Entity\Part;

final class AccrueIncomePartsListener implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(IncomeAccrued $event): void
    {
        $income = $this->registry->get(Income::class, $event->incomeId);

        foreach ($income->getIncomeParts() as $incomePart) {
            $partId = $incomePart->partId;

            $storagePart = $this->registry->find(Part::class, $partId);

            if (null === $storagePart) {
                $storagePart = new Part($partId);
                $this->registry->add($storagePart);
            }

            $storagePart->increase(
                $incomePart->getQuantity(),
                MotionSource::income($incomePart->toId()),
            );
        }
    }
}
