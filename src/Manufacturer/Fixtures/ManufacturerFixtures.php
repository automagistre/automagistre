<?php

declare(strict_types=1);

namespace App\Manufacturer\Fixtures;

use App\Manufacturer\Entity\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class ManufacturerFixtures extends Fixture implements FixtureGroupInterface
{
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
        $manufacturer = new Manufacturer('Nissan');

        $this->addReference('manufacturer-1', $manufacturer);

        $manager->persist($manufacturer);
        $manager->flush();
    }
}
