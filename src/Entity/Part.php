<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Price;
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

    /**
     * @var Manufacturer
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
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
     * @ORM\Column(name="partnumber_disp", length=64, nullable=true)
     */
    private $partnumberDisp;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(length=30)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="negative", type="boolean", nullable=true)
     */
    private $negative;

    /**
     * @var bool
     *
     * @ORM\Column(name="fractional", type="boolean", nullable=true)
     */
    private $fractional;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantity;

    /**
     * @var int
     *
     * @ORM\Column(name="reserved", type="integer", nullable=false)
     */
    private $reserved = 0;

    public function __toString(): string
    {
        return (string) $this->getName();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setPrice(Money $price): void
    {
        $this->price = $price;
    }
}
