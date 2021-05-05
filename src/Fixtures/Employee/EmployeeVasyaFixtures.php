<?php

declare(strict_types=1);

namespace App\Fixtures\Employee;

use App\Customer\Entity\OperandId;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Employee\Entity\Salary;
use App\Employee\Entity\SalaryId;
use App\Fixtures\Customer\PersonVasyaFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class EmployeeVasyaFixtures extends Fixture
{
    public const ID = '1eab59c7-f45f-6b60-bf4a-0242c0a8100a';
    public const PERSON_ID = PersonVasyaFixtures::ID;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $employeeId = EmployeeId::from(self::ID);

        $employee = new Employee($employeeId);
        $employee->setPersonId(OperandId::from(self::PERSON_ID));
        $employee->setRatio(50);

        $manager->persist(
            new Salary(
                SalaryId::generate(),
                $employeeId,
                5,
                new Money('5000', new Currency('RUB')),
            ),
        );

        $manager->persist($employee);
        $manager->flush();
    }
}
