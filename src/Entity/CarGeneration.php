<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarGeneration
{
    use Identity;

    /**
     * @var CarModel
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CarModel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $carModel;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(name="name", length=50, nullable=true)
     */
    private $name;

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCarModel(): ?CarModel
    {
        return $this->carModel;
    }

    public function setCarmodel(CarModel $carModel): void
    {
        $this->carModel = $carModel;
    }

    public function getDisplayName(): string
    {
        return sprintf('%s %s', $this->getCarModel()->getDisplayName(), $this->getName());
    }
}
