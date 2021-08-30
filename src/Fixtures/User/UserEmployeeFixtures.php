<?php

declare(strict_types=1);

namespace App\Fixtures\User;

use App\User\Entity\User;
use App\User\Entity\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserEmployeeFixtures extends Fixture
{
    public const ID = '1ea9478c-eca4-6f96-a221-3ab8c77b35e5';
    public const USERNAME = 'employee@automagistre.ru';
    public const PASSWORD = 'pa$$word';
    public const PASSWORD_HASH = '$2y$13$RO8v5ocI.PAoWqJDsfs0T.qbCemJhO/U3KgB672Y7CxDszFj3GCtK';
    public const ROLES = ['ROLE_ADMIN'];
    public const REFERENCE = 'user-employee';

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
