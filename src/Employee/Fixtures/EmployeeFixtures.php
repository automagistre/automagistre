<?php

declare(strict_types=1);

namespace App\Employee\Fixtures;

use App\Customer\Domain\Person;
use App\Doctrine\Registry;
use App\Entity\Tenant\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class EmployeeFixtures extends Fixture implements FixtureGroupInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
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
