<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Employee
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $hiredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firedAt;

    public function __construct()
    {
        $this->hiredAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function getHiredAt(): \DateTime
    {
        return $this->hiredAt;
    }

    public function getFiredAt(): ?\DateTime
    {
        return $this->firedAt;
    }

    public function getFullName(): string
    {
        return $this->person->getFullName();
    }

    public function fire()
    {
        $this->firedAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->person->getFullName();
    }
}
