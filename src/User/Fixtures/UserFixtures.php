<?php

declare(strict_types=1);

namespace App\User\Fixtures;

use App\Roles;
use App\Tenant\Tenant;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Generator;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserFixtures extends Fixture implements FixtureGroupInterface
{
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
        foreach ($this->users() as [$username, $roles, $tenants, $reference]) {
            $user = new User();
            $this->addReference($reference, $user);
            $user->setUsername($username);
            $user->setRoles($roles);
            $user->changePassword('pa$$word', $this->encoderFactory->getEncoder($user));

            foreach ($tenants as $tenant) {
                $user->addTenant($tenant);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function users(): Generator
    {
        yield ['admin@automagistre.ru', [Roles::ADMIN], [], 'user-admin'];
        yield ['employee@automagistre.ru', [Roles::EMPLOYEE], [Tenant::msk()], 'user-employee'];
    }
}
