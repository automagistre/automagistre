<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarGeneration
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var CarModel
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\CarModel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $carModel;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", length=50, nullable=true)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
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

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
