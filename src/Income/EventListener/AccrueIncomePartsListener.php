<?php

declare(strict_types=1);

namespace App\Income\EventListener;

use App\Income\Event\IncomeAccrued;
use App\Part\Event\PartAccrued;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\Motion;
use App\Storage\Enum\Source;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccrueIncomePartsListener implements EventSubscriberInterface
{
    private Registry $registry;

    private EventDispatcherInterface $dispatcher;

    public function __construct(Registry $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            IncomeAccrued::class => 'onIncomeAccrued',
        ];
    }

    public function onIncomeAccrued(IncomeAccrued $event): void
    {
        $em = $this->registry->manager(Motion::class);

        $income = $event->getSubject();

        foreach ($income->getIncomeParts() as $incomePart) {
            $partId = $incomePart->partId;
            $quantity = $incomePart->getQuantity();

            $motion = new Motion(
                $partId,
                $incomePart->getQuantity(),
                Source::income(),
                $incomePart->toId()->toUuid(),
            );
            $em->persist($motion);

            $em->flush();

            $this->dispatcher->dispatch(new PartAccrued($partId, [
                'quantity' => $quantity,
            ]));
        }
    }
}
