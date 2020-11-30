<?php

namespace App\Vehicle\Documents;

use App\Manufacturer\Documents\Manufacturer;
use App\Vehicle\Entity\VehicleId;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * @EmbeddedDocument
 */
class Vehicle
{
    /**
     * @Id(strategy="NONE", type="vehicle_id")
     */
    public VehicleId $id;

    /**
     * @EmbedOne(targetDocument=Manufacturer::class)
     */
    public Manufacturer $manufacturer;

    /**
     * @Field
     */
    public string $name;

    /**
     * @Field(nullable=true)
     */
    public ?string $localizedName;

    /**
     * @Field(nullable=true)
     */
    public ?string $caseName;

    /**
     * @Field(nullable=true)
     */
    public ?int $yearFrom;

    /**
     * @Field(nullable=true)
     */
    public ?int $yearTill;

    public function __construct(
        VehicleId $id,
        Manufacturer $manufacturer,
        string $name,
        ?string $localizedName,
        ?string $caseName,
        ?int $yearFrom,
        ?int $yearTill
    ) {
        $this->id = $id;
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->caseName = $caseName;
        $this->yearFrom = $yearFrom;
        $this->yearTill = $yearTill;
    }
}
