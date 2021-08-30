<?php

declare(strict_types=1);

namespace App\Fixtures\Employee;

use App\User\Entity\User;
use App\User\Entity\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AdminFixtures extends Fixture
{
    public const ID = '1ea94794-6491-68cc-9156-3ab8c77b35e5';
    public const USERNAME = 'admin@automagistre.ru';
    public const PASSWORD = 'pa$$word';
    public const ROLES = ['ROLE_ADMIN'];
    public const REFERENCE = 'user-admin';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            UserId::from(self::ID),
            self::ROLES,
            self::USERNAME,
        );

        $this->addReference(self::REFERENCE, $user);
        $manager->persist($user);
        $manager->flush();
    }
}
