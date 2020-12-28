<?php

declare(strict_types=1);

namespace App\Fixtures\Car;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Fixtures\Vehicle\NissanPrimeraFixture;
use App\Vehicle\Entity\VehicleId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class Primera2004Fixtures extends Fixture
{
    public const ID = '1ea88193-17fa-6b7a-ac1b-ba1ca6d07248';
    public const YEAR = 2004;
    public const VEHICLE_ID = NissanPrimeraFixture::ID;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $car = new Car(CarId::fromString(self::ID));
        $car->year = self::YEAR;
        $car->vehicleId = VehicleId::fromString(self::VEHICLE_ID);

        $this->addReference(__CLASS__, $car);

        $manager->persist($car);
        $manager->flush();
    }
}
