<?php

declare(strict_types=1);

namespace App\Vehicle\Domain;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Manufacturer\Domain\Manufacturer;
use Doctrine\ORM\Mapping as ORM;
use function sprintf;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="car_model",
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
    use Identity;

    /**
     * @ORM\Column(type="vehicle_id", unique=true)
     */
    public VehicleId $uuid;

    /**
     * @ORM\ManyToOne(targetEntity=Manufacturer::class)
     * @ORM\JoinColumn(nullable=false)
     */
    public ?Manufacturer $manufacturer = null;

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

    public function __construct()
    {
        $this->uuid = VehicleId::generate();
    }

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function getDisplayName(bool $withYears = true): string
    {
        $main = sprintf(
            '%s %s',
            $this->manufacturer->getName(),
            $this->localizedName ?? $this->name
        );

        $from = $this->yearFrom;
        $till = $this->yearTill;

        $years = $withYears && (null !== $from || null !== $till)
            ? sprintf(' (%s - %s)', $from ?? '...', $till ?? '...')
            : '';

        $case = null !== $this->caseName ? sprintf(' - %s', $this->caseName) : '';

        return $main.$case.$years;
    }
}
