<?php

declare(strict_types=1);

namespace App\User\DataFixtures;

use App\Roles;
use App\Tenant\Tenant;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Generator;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserFixtures extends Fixture
{
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->users() as [$username, $roles, $tenants]) {
            $user = new User();
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
        yield ['admin@automagistre.ru', [Roles::ADMIN], []];
        yield ['employee@automagistre.ru', [Roles::EMPLOYEE], [Tenant::msk()]];
    }
}
