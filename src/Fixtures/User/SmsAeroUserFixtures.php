<?php

declare(strict_types=1);

namespace App\Fixtures\User;

use App\User\Entity\User;
use App\User\Entity\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SmsAeroUserFixtures extends Fixture
{
    public const ID = '1ec036ef-08b3-67ce-8e11-02423aef0bb7';
    public const USERNAME = 'smsaero@automagistre.ru';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            UserId::from(self::ID),
            [],
            self::USERNAME,
        );

        $manager->persist($user);
        $manager->flush();
    }
}
