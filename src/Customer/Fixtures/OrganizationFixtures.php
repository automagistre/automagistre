<?php

declare(strict_types=1);

namespace App\Customer\Fixtures;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\Organization;
use App\Note\Entity\Note;
use App\Note\Enum\NoteType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class OrganizationFixtures extends Fixture
{
    public const ID = '1ea91f74-3fc0-6e46-96ae-5e6bd0ab745f';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $organization = new Organization(OperandId::fromString(self::ID));
        $organization->setName('Org 1');

        $this->addReference('organization-1', $organization);
        $manager->persist($organization);
        $manager->persist(new Note($organization->toId()->toUuid(), NoteType::info(), 'Organization Note'));

        $manager->flush();
    }
}
