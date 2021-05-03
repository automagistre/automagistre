<?php

declare(strict_types=1);

namespace App\Fixtures\Manufacturer;

use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ToyotaFixture extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea88057-31b5-6c6e-bc87-ba1ca6d07248';
    public const NAME = 'Toyota';
    public const LOCALIZED_NAME = 'Тойота';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [NissanFixture::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $manufacturer = new Manufacturer(
            ManufacturerId::from(self::ID),
            self::NAME,
            self::LOCALIZED_NAME,
        );

        $manager->persist($manufacturer);
        $manager->flush();
    }
}
