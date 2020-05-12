<?php

declare(strict_types=1);

namespace App\User\Fixtures;

use App\Roles;
use App\Tenant\Tenant;
use App\User\Domain\UserId;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Generator;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeFixtures extends Fixture implements FixtureGroupInterface
{
    public const ID = '1ea9478c-eca4-6f96-a221-3ab8c77b35e5';
    public const USERNAME = 'employee@automagistre.ru';
    public const PASSWORD = 'pa$$word';
    public const ROLES = [Roles::EMPLOYEE];
    public const REFERENCE = 'user-employee';

    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
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
        $user = new User(
            UserId::fromString(self::ID),
            self::ROLES,
            self::USERNAME,
            null
        );

        $user->changePassword(self::PASSWORD, $this->encoderFactory->getEncoder($user));
        $user->addTenant(Tenant::msk());

        $this->addReference(self::REFERENCE, $user);
        $manager->persist($user);
        $manager->flush();
    }

    private function users(): Generator
    {
        yield ['admin@automagistre.ru', [Roles::ADMIN], [], 'user-admin'];
        yield ['employee@automagistre.ru', [Roles::EMPLOYEE], [Tenant::msk()], 'user-employee'];
    }
}
