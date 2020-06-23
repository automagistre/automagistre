<?php

declare(strict_types=1);

namespace App\Employee\Fixtures;

use App\Customer\Entity\Person;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Shared\Doctrine\Registry;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EmployeeFixtures extends Fixture
{
    public const ID = '1eab59c7-f45f-6b60-bf4a-0242c0a8100a';

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
        assert($person instanceof Person);

        $employee = new Employee(EmployeeId::fromString(self::ID));
        $employee->setPerson($person);
        $employee->setRatio(50);

        $this->addReference('employee-1', $employee);

        $manager->persist($employee);
        $manager->flush();
    }
}
