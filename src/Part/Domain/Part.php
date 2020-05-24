<?php

declare(strict_types=1);

namespace App\Part\Domain;

use App\Entity\Discounted;
use App\Manufacturer\Domain\ManufacturerId;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Table(name="part", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"number", "manufacturer_id"})
 * })
 * @ORM\Entity
 */
class Part implements Discounted
{
    /**
     * @ORM\Id()
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
     * @ORM\Embedded(class=Money::class)
     */
    public Money $price;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $discount;

    public function __construct(
        PartId $id,
        ManufacturerId $manufacturerId,
        string $name,
        PartNumber $number,
        bool $universal,
        Money $price,
        Money $discount
    ) {
        $this->id = $id;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->number = $number;
        $this->universal = $universal;
        $this->price = $price;
        $this->discount = $discount;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function toId(): PartId
    {
        return $this->id;
    }

    public function update(string $name, bool $universal, Money $price, Money $discount): void
    {
        $this->name = $name;
        $this->universal = $universal;
        $this->price = $price;
        $this->discount = $discount;
    }

    public function equals(self $part): bool
    {
        return $part->toId()->equal($this->id);
    }

    public function isDiscounted(): bool
    {
        return $this->discount->isPositive();
    }

    public function discount(?Money $discount = null): Money
    {
        return $this->discount;
    }
}
