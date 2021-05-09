<?php

declare(strict_types=1);

namespace App\Employee\EventListener;

use App\Customer\Entity\Person;
use App\Employee\Entity\Employee;
use App\Employee\Event\EmployeeCreated;
use App\Employee\Event\EmployeeFired;
use App\MessageBus\MessageHandler;
use App\Shared\Doctrine\Registry;

final class EmployeeContractorListener implements MessageHandler
{
    public function __construct(private Registry $registry)
    {
    }

    public function __invoke(EmployeeCreated | EmployeeFired $event): void
    {
        $entity = $this->registry->get(Employee::class, $event->employeeId);
        $personId = $entity->getPersonId();

        if (null === $personId) {
            return;
        }

        $person = $this->registry->get(Person::class, $personId);

        $person->setContractor($event instanceof EmployeeCreated);
    }
}
