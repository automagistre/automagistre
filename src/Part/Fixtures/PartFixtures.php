<?php

declare(strict_types=1);

namespace App\Part\Fixtures;

use App\Entity\Landlord\Part;
use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Fixtures\ManufacturerFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class PartFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            ManufacturerFixtures::class,
        ];
    }

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
        $manufacturer = $this->getReference('manufacturer-1');
        assert($manufacturer instanceof Manufacturer);

        $part = new Part();
        $part->setManufacturer($manufacturer);
        $part->setName('Part 1');
        $part->setNumber('part1number');

        $this->addReference('part-1', $part);
        $manager->persist($part);
        $manager->flush();
    }
}
