<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Manufacturer
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
     * @var string
     *
     * @ORM\Column(name="name", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", length=25, nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(length=25, nullable=true, unique=true)
     */
    private $logoad;

    /**
     * @var string
     *
     * @ORM\Column(length=25, nullable=true, unique=true)
     */
    private $logoem;

    /**
     * @var bool
     *
     * @ORM\Column(name="bitoriginal", type="boolean", nullable=true)
     */
    private $bitoriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="logopl", length=25, nullable=true)
     */
    private $logopl;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
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

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
