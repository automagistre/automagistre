<?php

declare(strict_types=1);

namespace App\Car\Fixtures;

use App\Car\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class CarFixtures extends Fixture implements FixtureGroupInterface
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
        $car = new Car();
        $this->addReference('car-1', $car);

        $manager->persist($car);
        $manager->flush();
    }
}
