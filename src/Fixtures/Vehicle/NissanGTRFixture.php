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

final class NissanGTRFixture extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea88042-e4ff-6faa-80f4-ba1ca6d07248';
    public const MANUFACTURER_ID = NissanFixture::ID;
    public const NAME = 'GTR';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            NissanPrimeraFixture::class,
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
            null,
            null,
            null,
        );

        $this->addReference(__CLASS__, $model);

        $manager->persist($model);
        $manager->flush();
    }
}
