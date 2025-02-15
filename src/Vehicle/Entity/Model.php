<?php

declare(strict_types=1);

namespace App\Vehicle\Entity;

use App\Costil;
use App\Manufacturer\Entity\ManufacturerId;
use Doctrine\ORM\Mapping as ORM;
use App\Keycloak\Entity\UserId;
use DateTimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="vehicle_model",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"manufacturer_id", "name", "case_name"})
 *     }
 * )
 */
class Model
{
    /**
     * @ORM\Id
     * @ORM\Column
     */
    public VehicleId $id;

    /**
     * @ORM\Column
     */
    public ManufacturerId $manufacturerId;

    /**
     * @ORM\Column
     */
    public string $name;

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

    /**
     * @ORM\Column
     */
    public UserId $createdBy;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public DateTimeImmutable $createdAt;

    public function __construct(
        VehicleId $uuid,
        ManufacturerId $manufacturerId,
        string $name,
        ?string $localizedName,
        ?string $caseName,
        ?int $yearFrom,
        ?int $yearTill,
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
}
