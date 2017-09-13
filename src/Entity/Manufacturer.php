<?php

declare(strict_types=1);

namespace App\Entity;

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

    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }
}
