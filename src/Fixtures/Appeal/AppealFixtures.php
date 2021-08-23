<?php

declare(strict_types=1);

namespace App\Fixtures\Appeal;

use App\Appeal\Entity\AppealId;
use App\Appeal\Entity\Call;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use libphonenumber\PhoneNumberUtil;

final class AppealFixtures extends Fixture
{
    public const ID = '1ec0433b-d880-62ba-a337-1ad5d00b1e11';
    public const PHONE = '+79261680000';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $entity = new Call(
            AppealId::from(self::ID),
            PhoneNumberUtil::getInstance()->parse(self::PHONE),
        );

        $manager->persist($entity);
        $manager->flush();
    }
}
