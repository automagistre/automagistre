<?php

declare(strict_types=1);

namespace App\Income\EventListener;

use App\Income\Entity\Income;
use App\Income\Event\IncomeAccrued;
use App\Part\Domain\Part;
use App\Part\Event\PartAccrued;
use App\Shared\Doctrine\Registry;
use App\Storage\Entity\Motion;
use App\Storage\Enum\Source;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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

    public function onIncomeAccrued(GenericEvent $event): void
    {
        $em = $this->registry->manager(Motion::class);

        $income = $event->getSubject();
        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        foreach ($income->getIncomeParts() as $incomePart) {
            /** @var Part $part */
            $part = $this->registry->findBy(Part::class, ['partId' => $incomePart->partId]);
            $quantity = $incomePart->getQuantity();

            $motion = new Motion(
                $part,
                $incomePart->getQuantity(),
                Source::income(),
                $incomePart->toId()->toUuid(),
            );
            $em->persist($motion);

            $em->flush();

            $this->dispatcher->dispatch(new PartAccrued($part, [
                'quantity' => $quantity,
            ]));
        }
    }
}
