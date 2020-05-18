<?php

declare(strict_types=1);

namespace App\Car\Fixtures;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class EmptyCarFixtures extends Fixture implements FixtureGroupInterface
{
    public const ID = '1ea8818c-bf1b-6820-b45f-ba1ca6d07248';

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $car = new Car(CarId::fromString(self::ID));
        $this->addReference('car-1', $car);

        $manager->persist($car);
        $manager->flush();
    }
}
