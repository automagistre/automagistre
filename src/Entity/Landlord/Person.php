<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhone;
use function sprintf;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 *
 * @UniqueEntity(fields={"telephone"}, message="Клиент с таким телефоном уже существует")
 */
class Person extends Operand
{
    /**
     * @var string
     *
     * @Assert\Length(max="32")
     *
     * @ORM\Column(length=32, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $lastname;

    /**
     * @var PhoneNumber
     *
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true, unique=true)
     */
    private $telephone;

    /**
     * @var PhoneNumber
     *
     * @AssertPhone
     *
     * @ORM\Column(type="phone_number", nullable=true)
     */
    private $officePhone;

    public function __toString(): string
    {
        $string = $this->getFullName();
        $email = $this->getEmail();

        if (null !== $email) {
            $string .= sprintf(' (%s)', $email);
        }

        return $string;
    }

    public function isType(string $type): bool
    {
        return 'person' === $type;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
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

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->lastname, $this->firstname);
    }
}
