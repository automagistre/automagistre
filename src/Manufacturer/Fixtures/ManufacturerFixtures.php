<?php

declare(strict_types=1);

namespace App\Manufacturer\Fixtures;

use App\Manufacturer\Entity\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Generator;

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
        foreach ($this->generate() as [$name, $localizedName]) {
            $manufacturer = new Manufacturer($name, $localizedName);

            $this->addReference($name, $manufacturer);

            $manager->persist($manufacturer);
        }

        $manager->flush();
    }

    private function generate(): Generator
    {
        yield ['Infinity', 'Инфинити'];
        yield ['Lexus', 'Лексус'];
        yield ['Nissan', 'Ниссан'];
        yield ['Toyota', 'Тойота'];
    }
}
