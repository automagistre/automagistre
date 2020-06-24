<?php

declare(strict_types=1);

namespace App\Employee\EventListener;

use App\Customer\Entity\Person;
use App\Employee\Entity\Employee;
use App\Employee\Event\EmployeeCreated;
use App\Employee\Event\EmployeeFired;
use App\Shared\Doctrine\Registry;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeContractorListener implements EventSubscriberInterface
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

        $personId = $entity->getPersonId();
        if (null === $personId) {
            return;
        }

        /** @var Person $person */
        $person = $this->registry->getBy(Person::class, ['uuid' => $personId]);

        $person->setContractor(EmployeeCreated::class === $eventName);

        $this->registry->manager(Person::class)->flush();
    }
}
