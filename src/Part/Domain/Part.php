<?php

declare(strict_types=1);

namespace App\Part\Domain;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Discounted;
use App\Manufacturer\Domain\ManufacturerId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use function preg_replace;
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
class Part implements Discounted
{
    use Identity;

    /**
     * @ORM\Column(type="part_id", unique=true)
     */
    public PartId $partId;

    /**
     * @ORM\Column(type="manufacturer_id")
     */
    public ManufacturerId $manufacturerId;

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
     * @ORM\Embedded(class=Money::class)
     */
    public Money $price;

    /**
     * @var Collection<int, Part>
     *
     * @ORM\ManyToMany(targetEntity=Part::class)
     * @ORM\JoinTable
     */
    public iterable $relation;

    /**
     * @ORM\Embedded(class=Money::class)
     */
    private Money $discount;

    public function __construct(
        PartId $partId,
        ManufacturerId $manufacturerId,
        string $name,
        string $number,
        bool $universal,
        Money $price,
        Money $discount
    ) {
        $this->partId = $partId;
        $this->manufacturerId = $manufacturerId;
        $this->name = $name;
        $this->number = self::sanitize($number);
        $this->universal = $universal;
        $this->price = $price;
        $this->relation = new ArrayCollection();
        $this->discount = $discount;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function toId(): PartId
    {
        return $this->partId;
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
        return $part->getId() === $this->id;
    }

    public static function sanitize(string $number): string
    {
        return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $number));
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
