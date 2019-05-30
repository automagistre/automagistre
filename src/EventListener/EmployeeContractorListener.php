<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Tenant\Employee;
use App\Event\EmployeeCreated;
use App\Event\EmployeeFired;
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
            throw new \LogicException('Employee expected');
        }

        $entity->getPerson()->setContractor(EmployeeCreated::class === $eventName);
    }
}
