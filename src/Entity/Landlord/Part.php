<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Discount;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="part", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="part_idx", columns={"number", "manufacturer_id"})
 * })
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"manufacturer", "number"})
 */
class Part
{
    use Identity;
    use Price;
    use Discount;

    /**
     * @var Manufacturer
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Manufacturer")
     * @ORM\JoinColumn
     */
    private $manufacturer;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(length=30)
     */
    private $number;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $universal = false;

    /**
     * @var Part[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Landlord\Part")
     * @ORM\JoinTable
     */
    private $relation;

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function displayName(): string
    {
        return \sprintf('%s - %s (%s)', $this->manufacturer, $this->name, $this->number);
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
        $this->number = \strtoupper(\preg_replace('/[^a-zA-Z0-9]/', '', $number));
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
