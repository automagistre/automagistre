<?php

namespace AppBundle\Entity;

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

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $firstname
     */
    public function setName(string $name)
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

    /**
     * @return string
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone(string $telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
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
