<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarModel
{
    use Identity;

    /**
     * @var Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Manufacturer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $manufacturer;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(name="name", length=30)
     */
    private $name;

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getDisplayName(): string
    {
        return sprintf('%s %s', $this->manufacturer->getName(), $this->getName());
    }
}
