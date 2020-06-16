<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\MC\Entity\McEquipment;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Fixtures\NissanGTRFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class EquipmentFixtures extends Fixture implements FixtureGroupInterface
{
    public const VEHICLE_ID = NissanGTRFixture::ID;

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
        $equipment = new McEquipment();
        $equipment->vehicleId = VehicleId::fromString(self::VEHICLE_ID);
        $equipment->period = 10;

        $this->addReference('equipment-1', $equipment);

        $manager->persist($equipment);
        $manager->flush();
    }
}
