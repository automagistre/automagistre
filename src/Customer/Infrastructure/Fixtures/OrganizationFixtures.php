<?php

declare(strict_types=1);

namespace App\Customer\Infrastructure\Fixtures;

use App\Customer\Domain\OperandId;
use App\Customer\Domain\OperandNote;
use App\Customer\Domain\Organization;
use App\Enum\NoteType;
use App\User\Entity\User;
use App\User\Fixtures\UserFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class OrganizationFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const ID = '1ea91f74-3fc0-6e46-96ae-5e6bd0ab745f';

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

        $organization = new Organization(OperandId::fromString(self::ID));
        $organization->setName('Org 1');

        $this->addReference('organization-1', $organization);
        $manager->persist($organization);
        $manager->persist(new OperandNote($organization, $user, NoteType::info(), 'Organization Note'));

        $manager->flush();
    }
}
