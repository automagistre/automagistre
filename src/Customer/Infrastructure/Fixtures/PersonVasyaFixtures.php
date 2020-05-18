<?php

declare(strict_types=1);

namespace App\Customer\Infrastructure\Fixtures;

use App\Customer\Domain\OperandId;
use App\Customer\Domain\OperandNote;
use App\Customer\Domain\Person;
use App\Shared\Enum\NoteType;
use App\User\Domain\UserId;
use App\User\Fixtures\EmployeeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PersonVasyaFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const ID = '1ea91f71-dfaf-6374-837c-5e6bd0ab745f';
    public const CREATED_BY = EmployeeFixtures::ID;

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            OrganizationFixtures::class, // Organization must have ID = 1
        ];
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
        $person = new Person(OperandId::fromString(self::ID));
        $person->setFirstname('Vasya');

        $this->addReference('person-1', $person);

        $manager->persist($person);
        $manager->persist(new OperandNote($person, UserId::fromString(self::CREATED_BY), NoteType::info(), 'Person Note'));

        $manager->flush();
    }
}
