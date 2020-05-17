<?php

declare(strict_types=1);

namespace App\Employee\EventListener;

use App\Employee\Entity\Employee;
use App\Event\EmployeeCreated;
use App\Event\EmployeeFired;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeContractorListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EmployeeCreated::class => 'onEmployeeCreatedOrFired',
            EmployeeFired::class => 'onEmployeeCreatedOrFired',
        ];
    }

    public function onEmployeeCreatedOrFired(GenericEvent $event, string $eventName): void
    {
        $entity = $event->getSubject();
        if (!$entity instanceof Employee) {
            throw new LogicException('Employee expected');
        }

        $person = $entity->getPerson();
        if (null === $person) {
            return;
        }

        $person->setContractor(EmployeeCreated::class === $eventName);
    }
}
