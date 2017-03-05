<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="person", indexes={
 *     @ORM\Index(name="lastname_idx", columns={"lastname"}),
 *     @ORM\Index(name="firstname_idx", columns={"firstname"}),
 *     @ORM\Index(name="phone_idx", columns={"telephone"}),
 *     @ORM\Index(name="sprite_id", columns={"sprite_id"})
 * })
 * @ORM\Entity
 */
class Person
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
     * @ORM\Column(length=24, nullable=true)
     */
    private $officePhone;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $spriteId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
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
    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    /**
     * @param string $officePhone
     */
    public function setOfficePhone(string $officePhone)
    {
        $this->officePhone = $officePhone;
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

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->lastname, $this->firstname);
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getFullName(), $this->getTelephone());
    }
}
