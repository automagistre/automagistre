<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Manufacturer\Entity\ManufacturerId;
use App\Part\Enum\Unit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="part", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"number", "manufacturer_id"})
 * })
 * @ORM\Entity
 */
class Part
{
    /**
     * @ORM\Id
     * @ORM\Column(type="part_id", unique=true)
     */
    public PartId $id;

    /**
     * @ORM\Column(type="manufacturer_id")
     */
    public ManufacturerId $manufacturerId;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="part_number", length=30)
     */
    public PartNumber $number;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $universal = false;

    /**
     * @ORM\Column(type="unit_enum")
     */
    public Unit $unit;

    public function __construct(
        PartId $id,
        ManufacturerId $manufacturerId,
        string $name,
        PartNumber $number,
        bool $universal,
        Unit $unit,
    ) {
        $this->id = $id;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->number = $number;
        $this->universal = $universal;
        $this->unit = $unit;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function toId(): PartId
    {
        return $this->id;
    }

    public function update(string $name, bool $universal, Unit $unit): void
    {
        $this->name = $name;
        $this->universal = $universal;
        $this->unit = $unit;
    }

    public function equals(PartId | self $part): bool
    {
        $id = $part instanceof self ? $part->id : $part;

        return $this->toId()->equals($id);
    }
}
