<?php

declare(strict_types=1);

namespace App\Operand\Fixtures;

use App\Entity\Landlord\OperandNote;
use App\Entity\Landlord\Organization;
use App\Entity\Landlord\Person;
use App\Enum\NoteType;
use App\User\Entity\User;
use App\User\Fixtures\UserFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class OperandFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['landlord'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user-employee');
        assert($user instanceof User);

        $organization = new Organization();
        $organization->setName('Org 1');

        $this->addReference('organization-1', $organization);
        $manager->persist($organization);
        $manager->persist(new OperandNote($organization, $user, NoteType::info(), 'Organization Note'));
        $manager->flush();

        $person = new Person();
        $person->setFirstname('Person 1');

        $this->addReference('person-1', $person);
        $manager->persist($person);
        $manager->persist(new OperandNote($person, $user, NoteType::info(), 'Person Note'));

        $manager->flush();
    }
}
