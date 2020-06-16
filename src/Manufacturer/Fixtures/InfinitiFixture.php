<?php

declare(strict_types=1);

namespace App\Manufacturer\Fixtures;

use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class InfinitiFixture extends Fixture
{
    public const ID = '1ea88050-34e6-68f4-a718-ba1ca6d07248';
    public const NAME = 'Infiniti';
    public const LOCALIZED_NAME = 'Инфинити';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $manufacturer = new Manufacturer(
            ManufacturerId::fromString(self::ID),
            self::NAME,
            self::LOCALIZED_NAME,
        );

        $manager->persist($manufacturer);
        $manager->flush();
    }
}
