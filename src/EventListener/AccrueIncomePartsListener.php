<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Tenant\Income;
use App\Entity\Tenant\MotionIncome;
use App\Events;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccrueIncomePartsListener implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

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
            Events::INCOME_ACCRUED => 'onIncomeAccrued',
        ];
    }

    public function onIncomeAccrued(GenericEvent $event): void
    {
        $em = $this->registry->manager(MotionIncome::class);

        $income = $event->getSubject();
        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        foreach ($income->getIncomeParts() as $incomePart) {
            $part = $incomePart->getPart();
            $quantity = $incomePart->getQuantity();

            $em->persist($motion = new MotionIncome($incomePart));

            $incomePart->accrue($motion);

            /* @noinspection DisconnectedForeachInstructionInspection */
            $em->flush();

            $this->dispatcher->dispatch(Events::PART_ACCRUED, new GenericEvent($part, [
                'quantity' => $quantity,
            ]));
        }
    }
}
