<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class CarModel
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
     * @var CarManufacturer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CarManufacturer")
     * @ORM\JoinColumn()
     */
    private $carmake;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", length=30, nullable=true)
     */
    private $name;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return CarManufacturer
     */
    public function getCarmake(): ?CarManufacturer
    {
        return $this->carmake;
    }

    /**
     * @param CarManufacturer $carmake
     */
    public function setCarmake(CarManufacturer $carmake)
    {
        $this->carmake = $carmake;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
