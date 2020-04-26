<?php

declare(strict_types=1);

namespace App\Manufacturer\Infrastructure\Fixtures;

use App\Manufacturer\Domain\Manufacturer;
use App\Manufacturer\Domain\ManufacturerId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NissanFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const ID = '1ea88058-1c1f-6f20-9482-ba1ca6d07248';
    public const NAME = 'Nissan';
    public const LOCALIZED_NAME = 'Ниссан';
    public const REF = __CLASS__;

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
        return [LexusFixture::class];
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

        $this->addReference(self::REF, $manufacturer);

        $manager->persist($manufacturer);
        $manager->flush();
    }
}
