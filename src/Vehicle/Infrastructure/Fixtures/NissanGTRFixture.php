<?php

declare(strict_types=1);

namespace App\Vehicle\Infrastructure\Fixtures;

use App\Manufacturer\Domain\ManufacturerId;
use App\Manufacturer\Infrastructure\Fixtures\NissanFixture;
use App\Vehicle\Domain\Model;
use App\Vehicle\Domain\VehicleId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class NissanGTRFixture extends Fixture implements FixtureGroupInterface
{
    public const ID = '1ea88042-e4ff-6faa-80f4-ba1ca6d07248';
    public const MANUFACTURER_ID = NissanFixture::ID;
    public const NAME = 'GTR';

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['landlord'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $model = new Model(
            VehicleId::fromString(self::ID),
            ManufacturerId::fromString(self::MANUFACTURER_ID),
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
