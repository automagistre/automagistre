<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Entity\Embeddable\Requisite;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhone;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Organization extends Operand
{
    /**
     * @var Requisite
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Entity\Embeddable\Requisite")
     */
    public $requisite;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $address;

    /**
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true)
     */
    private ?PhoneNumber $telephone = null;

    /**
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true)
     */
    private ?PhoneNumber $officePhone = null;

    public function __construct()
    {
        $this->requisite = new Requisite();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function isType(string $type): bool
    {
        return 'organization' === $type;
    }

    public function getFullName(): string
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getTelephone(): ?PhoneNumber
    {
        return $this->telephone;
    }

    public function setTelephone(?PhoneNumber $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getOfficePhone(): ?PhoneNumber
    {
        return $this->officePhone;
    }

    public function setOfficePhone(?PhoneNumber $officePhone): void
    {
        $this->officePhone = $officePhone;
    }
}
