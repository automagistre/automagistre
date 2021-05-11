<?php

declare(strict_types=1);

namespace App\Storage\Messages;

use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\InventorizationPartView;
use App\Storage\Entity\MotionSource;
use App\Storage\Entity\Part;
use App\Storage\Event\InventorizationClosed;

final class ActualizeWarehouseOnInventorizationClosed implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(InventorizationClosed $event): void
    {
        /** @var InventorizationPartView[] $inventorizationParts */
        $inventorizationParts = $this->registry->findBy(InventorizationPartView::class, [
            'inventorizationId' => $event->inventorizationId,
        ]);

        foreach ($inventorizationParts as $inventorizationPart) {
            $storagePart = $this->registry->get(Part::class, $inventorizationPart->partId);

            $storagePart->actualize(
                $inventorizationPart->quantity,
                MotionSource::inventorization($inventorizationPart->inventorizationId),
            );
        }
    }
}
