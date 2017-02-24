<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="client", indexes={@ORM\Index(name="EID_IDX", columns={"eid"}), @ORM\Index(name="IDX_CLIENT_PERSON", columns={"person_id"})})
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     * @ORM\JoinColumn()
     */
    private $person;

    /**
     * @var int
     *
     * @ORM\Column(name="wallet", type="integer", nullable=false)
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
     * @var int
     *
     * @ORM\Column(name="eid", type="integer", nullable=true)
     */
    private $eid;

    /**
     * @var int
     *
     * @ORM\Column(name="referal_client_id", type="integer", nullable=true)
     */
    private $referalClientId;

    /**
     * @var bool
     *
     * @ORM\Column(name="ref_bonus", type="boolean", nullable=true)
     */
    private $refBonus;

    /**
     * @var int
     *
     * @ORM\Column(name="point_id", type="integer", nullable=true)
     */
    private $pointId;

    /**
     * @return int
     */
    public function getId(): int
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
        return $this->employee;
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
        return $this->person->getFullName();
    }
}
