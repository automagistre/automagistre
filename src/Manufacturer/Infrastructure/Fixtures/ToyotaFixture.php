<?php

declare(strict_types=1);

namespace App\Manufacturer\Infrastructure\Fixtures;

use App\Manufacturer\Domain\Manufacturer;
use App\Manufacturer\Domain\ManufacturerId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ToyotaFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const ID = '1ea88057-31b5-6c6e-bc87-ba1ca6d07248';
    public const NAME = 'Toyota';
    public const LOCALIZED_NAME = 'Тойота';

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
            ManufacturerId::fromString(self::ID),
            self::NAME,
            self::LOCALIZED_NAME,
        );

        $manager->persist($manufacturer);
        $manager->flush();
    }
}
