<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Client
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
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Person")
     * @ORM\JoinColumn()
     */
    private $person;

    /**
     * @var int
     *
     * @ORM\Column(name="wallet", type="integer")
     */
    private $wallet = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="employee", type="boolean", nullable=true)
     */
    private $employee = false;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson(): ?Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;
    }

    /**
     * @return int
     */
    public function getWallet(): ?int
    {
        return $this->wallet;
    }

    /**
     * @param int $wallet
     */
    public function setWallet(int $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isEmployee(): bool
    {
        return $this->employee ?: false;
    }

    /**
     * @param bool $employee
     */
    public function setEmployee(bool $employee)
    {
        $this->employee = $employee;
    }

    public function __toString(): string
    {
        return (string) $this->person;
    }
}
