<?php

declare(strict_types=1);

namespace App\Fixtures\Mc;

use App\Fixtures\Vehicle\NissanGTRFixture;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McEquipmentId;
use App\Publish\Entity\Publish;
use App\Vehicle\Entity\VehicleId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EquipmentFixtures extends Fixture
{
    public const ID = '1eab7adc-b60e-616e-8fb9-0242c0a81005';
    public const VEHICLE_ID = NissanGTRFixture::ID;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $equipment = new McEquipment(
            McEquipmentId::fromString(self::ID),
        );
        $equipment->vehicleId = VehicleId::fromString(self::VEHICLE_ID);
        $equipment->period = 10;

        $this->addReference('equipment-1', $equipment);

        $manager->persist(Publish::create($equipment->toId()->toUuid(), true));
        $manager->persist($equipment);
        $manager->flush();
    }
}
