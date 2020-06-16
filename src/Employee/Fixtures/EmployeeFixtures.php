<?php

declare(strict_types=1);

namespace App\Employee\Fixtures;

use App\Customer\Entity\Person;
use App\Employee\Entity\Employee;
use App\Shared\Doctrine\Registry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EmployeeFixtures extends Fixture
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $person = $this->registry->manager(Person::class)->getReference(Person::class, 2);

        $employee = new Employee();
        $employee->setPerson($person);
        $employee->setRatio(50);

        $this->addReference('employee-1', $employee);

        $manager->persist($employee);
        $manager->flush();
    }
}
