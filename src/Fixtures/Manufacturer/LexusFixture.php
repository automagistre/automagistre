<?php

declare(strict_types=1);

namespace App\Fixtures\Manufacturer;

use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LexusFixture extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea88058-f18f-64c6-88ac-ba1ca6d07248';
    public const NAME = 'Lexus';
    public const LOCALIZED_NAME = 'Лексус';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [InfinitiFixture::class];
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
