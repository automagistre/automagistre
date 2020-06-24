<?php

declare(strict_types=1);

namespace App\Employee\Fixtures;

use App\Customer\Entity\OperandId;
use App\Customer\Fixtures\PersonVasyaFixtures;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EmployeeFixtures extends Fixture
{
    public const ID = '1eab59c7-f45f-6b60-bf4a-0242c0a8100a';
    public const PERSON_ID = PersonVasyaFixtures::ID;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $employee = new Employee(EmployeeId::fromString(self::ID));
        $employee->setPersonId(OperandId::fromString(self::PERSON_ID));
        $employee->setRatio(50);

        $this->addReference('employee-1', $employee);

        $manager->persist($employee);
        $manager->flush();
    }
}
