<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Organization extends Operand
{
    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @Assert\Email()
     *
     * @ORM\Column(nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(length=24, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $officePhone;

    public function getFullName(): string
    {
        return $this->getName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    public function setOfficePhone(string $officePhone): void
    {
        $this->officePhone = $officePhone;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
