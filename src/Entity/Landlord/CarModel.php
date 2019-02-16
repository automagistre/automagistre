<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="MANUFACTURER_CASE_IDX", columns={"manufacturer_id", "case_name"})
 *     }
 * )
 *
 * @UniqueEntity(
 *     fields={"manufacturer", "caseName"},
 *     errorPath="caseName",
 *     message="Кузов {{ value }} у выбранного производителя уже существует."
 * )
 */
class CarModel
{
    use Identity;

    /**
     * @var Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Manufacturer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $manufacturer;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $localizedName;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $caseName;

    /**
     * @var int|null
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $yearFrom;

    /**
     * @var int|null
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $yearTill;

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function getDisplayName(bool $withYears = true): string
    {
        $main = \sprintf(
            '%s %s',
            $this->manufacturer->getName(),
            $this->getName() ?? $this->getLocalizedName()
        );

        $from = $this->getYearFrom();
        $till = $this->getYearTill();

        $years = $withYears && (null !== $from || null !== $till)
            ? \sprintf(' (%s - %s)', $from ?? '...', $till ?? '...')
            : '';

        $case = null !== $this->caseName ? \sprintf(' - %s', $this->caseName) : '';

        return $main.$case.$years;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getLocalizedName(): ?string
    {
        return $this->localizedName;
    }

    public function setLocalizedName(?string $localizedName): void
    {
        $this->localizedName = $localizedName;
    }

    public function getCaseName(): ?string
    {
        return $this->caseName;
    }

    public function setCaseName(?string $caseName): void
    {
        $this->caseName = $caseName;
    }

    public function getYearFrom(): ?int
    {
        return $this->yearFrom;
    }

    public function setYearFrom(?int $yearFrom): void
    {
        $this->yearFrom = $yearFrom;
    }

    public function getYearTill(): ?int
    {
        return $this->yearTill;
    }

    public function setYearTill(?int $yearTill): void
    {
        $this->yearTill = $yearTill;
    }
}
