<?php

declare(strict_types=1);

namespace App\Vehicle\Entity;

use App\Costil;
use App\Manufacturer\Entity\ManufacturerId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="vehicle_model",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"manufacturer_id", "case_name"})
 *     }
 * )
 *
 * @UniqueEntity(
 *     fields={"manufacturer", "caseName"},
 *     errorPath="caseName",
 *     message="Кузов {{ value }} у выбранного производителя уже существует."
 * )
 */
class Model
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="vehicle_id")
     */
    public VehicleId $id;

    /**
     * @ORM\Column(type="manufacturer_id")
     */
    public ManufacturerId $manufacturerId;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    public ?string $name = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $localizedName = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $caseName = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    public ?int $yearFrom = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    public ?int $yearTill = null;

    public function __construct(
        VehicleId $uuid,
        ManufacturerId $manufacturerId,
        string $name,
        ?string $localizedName,
        ?string $caseName,
        ?int $yearFrom,
        ?int $yearTill
    ) {
        $this->id = $uuid;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->caseName = $caseName;
        $this->yearFrom = $yearFrom;
        $this->yearTill = $yearTill;
    }

    public function __toString()
    {
        return Costil::display($this->id);
    }

    public function toId(): VehicleId
    {
        return $this->id;
    }

    public function update(
        string $name,
        ?string $localizedName,
        ?string $caseName,
        ?int $yearFrom,
        ?int $yearTill
    ): void {
        $this->name = $name;
        $this->localizedName = $localizedName;
        $this->caseName = $caseName;
        $this->yearFrom = $yearFrom;
        $this->yearTill = $yearTill;
    }
}
