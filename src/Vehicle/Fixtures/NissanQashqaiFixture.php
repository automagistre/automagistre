<?php

declare(strict_types=1);

namespace App\Vehicle\Fixtures;

use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Fixtures\NissanFixture;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class NissanQashqaiFixture extends Fixture
{
    public const ID = '1ea88045-a0e2-60ea-9a06-ba1ca6d07248';
    public const MANUFACTURER_ID = NissanFixture::ID;
    public const NAME = 'Qashqai';
    public const LOCALIZED_NAME = 'Кашкай';
    public const CASE_NAME = 'J10';
    public const YEAR_FROM = 2006;
    public const YEAR_TILL = 2013;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $model = new Model(
            VehicleId::fromString(self::ID),
            ManufacturerId::fromString(self::MANUFACTURER_ID),
            self::NAME,
            self::LOCALIZED_NAME,
            self::CASE_NAME,
            self::YEAR_FROM,
            self::YEAR_TILL,
        );

        $manager->persist($model);
        $manager->flush();
    }
}
