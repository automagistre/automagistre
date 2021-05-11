<?php

declare(strict_types=1);

namespace App\Fixtures\Employee;

use App\User\Entity\User;
use App\User\Entity\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ServiceUserFixtures extends Fixture
{
    public const ID = '59861141-83b2-416c-b672-8ba8a1cb76b2';
    public const USERNAME = 'service@automagistre.ru';
    public const ROLES = [];

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

        $manager->persist($user);
        $manager->flush();
    }
}
