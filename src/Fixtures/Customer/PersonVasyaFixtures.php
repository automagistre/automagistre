<?php

declare(strict_types=1);

namespace App\Fixtures\Customer;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\Person;
use App\Note\Entity\Note;
use App\Note\Enum\NoteType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PersonVasyaFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea91f71-dfaf-6374-837c-5e6bd0ab745f';

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
    public function load(ObjectManager $manager): void
    {
        $person = new Person(OperandId::fromString(self::ID));
        $person->setFirstname('Vasya');

        $this->addReference('person-1', $person);

        $manager->persist($person);
        $manager->persist(new Note($person->toId()->toUuid(), NoteType::info(), 'Person Note'));

        $manager->flush();
    }
}
