<?php

declare(strict_types=1);

namespace App\Part\Domain;

use App\Doctrine\ORM\Mapping\Traits\Discount;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Manufacturer\Domain\Manufacturer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function preg_replace;
use function sprintf;
use function strtoupper;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="part", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"number", "manufacturer_id"})
 * })
 * @ORM\Entity
 *
 * @UniqueEntity(
 *     fields={"manufacturer", "number"},
 *     errorPath="number",
 *     message="Запчасть {{ value }} у выбранного производителя уже существует."
 * )
 */
class Part
{
    use Identity;
    use Discount;

    /**
     * @ORM\Column(type="part_id", unique=true)
     */
    public PartId $partId;

    /**
     * @ORM\ManyToOne(targetEntity=Manufacturer::class)
     * @ORM\JoinColumn
     */
    public Manufacturer $manufacturer;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(length=30)
     */
    public string $number;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $universal = false;

    /**
     * @var Collection<int, Part>
     *
     * @ORM\ManyToMany(targetEntity=Part::class)
     * @ORM\JoinTable
     */
    public iterable $relation;

    /**
     *
     * @ORM\Embedded(class=Money::class)
     */
    private Money $price;

    public function __construct(
        Manufacturer $manufacturer,
        string $name,
        string $number,
        bool $universal,
        Money $price,
        Money $discount
    ) {
        $this->partId = PartId::generate();
        $this->manufacturer = $manufacturer;
        $this->name = $name;
        $this->setNumber($number);
        $this->universal = $universal;
        $this->price = $price;
        $this->relation = new ArrayCollection();
        $this->discount = $discount;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function displayName(): string
    {
        return sprintf('%s - %s (%s)', (string) $this->manufacturer, $this->name, $this->number);
    }

    public function equals(self $part): bool
    {
        return $part->getId() === $this->id;
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $number));
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }

    public function isUniversal(): bool
    {
        return $this->universal;
    }

    public function setUniversal(bool $universal): void
    {
        $this->universal = $universal;
    }
}
