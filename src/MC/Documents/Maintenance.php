<?php

declare(strict_types=1);

namespace App\MC\Documents;

use App\MC\Entity\McEquipmentId;
use App\Vehicle\Documents\Vehicle;
use App\Vehicle\Entity\Embedded\Engine;
use App\Vehicle\Enum\DriveWheelConfiguration;
use App\Vehicle\Enum\Transmission;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *     collection="maintenance",
 * )
 */
class Maintenance
{
    /**
     * @ODM\Id(strategy="NONE", type="mc_equipment_id")
     */
    public McEquipmentId $id;

    /**
     * @ODM\EmbedOne(targetDocument=Vehicle::class)
     */
    public Vehicle $vehicle;

    /**
     * @ODM\EmbedOne(targetDocument=Engine::class)
     */
    public Engine $engine;

    /**
     * @ODM\Field(type="car_transmission_enum")
     */
    public Transmission $transmission;

    /**
     * @ODM\Field(type="car_wheel_drive_enum")
     */
    public DriveWheelConfiguration $wheelDrive;

    /**
     * @var Collection<int, Work>
     *
     * @ODM\EmbedMany(targetDocument=Work::class)
     */
    public Collection $works;

    /**
     * @param Work[] $works
     */
    public function __construct(
        McEquipmentId $id,
        Vehicle $vehicle,
        Engine $engine,
        Transmission $transmission,
        DriveWheelConfiguration $wheelDrive,
        array $works
    ) {
        $this->id = $id;
        $this->vehicle = $vehicle;
        $this->engine = $engine;
        $this->transmission = $transmission;
        $this->wheelDrive = $wheelDrive;
        $this->works = new ArrayCollection();

        foreach ($works as $work) {
            $this->works->add($work);
        }
    }
}
