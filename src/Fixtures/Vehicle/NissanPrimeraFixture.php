<?php

declare(strict_types=1);

namespace App\Fixtures\Vehicle;

use App\Fixtures\Manufacturer\NissanFixture;
use App\Manufacturer\Entity\ManufacturerId;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NissanPrimeraFixture extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea88045-9807-6664-b601-ba1ca6d07248';
    public const MANUFACTURER_ID = NissanFixture::ID;
    public const NAME = 'Primera';
    public const CASE_NAME = 'P12';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            NissanQashqaiFixture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $model = new Model(
            VehicleId::from(self::ID),
            ManufacturerId::from(self::MANUFACTURER_ID),
            self::NAME,
            null,
            self::CASE_NAME,
            null,
            null,
        );

        $manager->persist($model);
        $manager->flush();
    }
}
