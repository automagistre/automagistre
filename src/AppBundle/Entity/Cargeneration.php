<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="cargeneration", indexes={@ORM\Index(name="IDX_GENERAT_PARENT", columns={"carmodel_id"}), @ORM\Index(name="IDX_GENERAT_FOLDER", columns={"folder"})})
 * @ORM\Entity
 */
class Cargeneration
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
     * @var Carmodel
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carmodel")
     * @ORM\JoinColumn()
     */
    private $carmodel;

    /**
     * @var int
     *
     * @ORM\Column(name="folder", type="integer", nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
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
     * @return Carmodel
     */
    public function getCarmodel(): ?Carmodel
    {
        return $this->carmodel;
    }

    /**
     * @param Carmodel $carmodel
     */
    public function setCarmodel(Carmodel $carmodel)
    {
        $this->carmodel = $carmodel;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
