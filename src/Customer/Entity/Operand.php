<?php

declare(strict_types=1);

namespace App\Customer\Entity;

use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"1": "Person", "2": "Organization"})
 */
abstract class Operand
{
    /**
     * @ORM\Id
     * @ORM\Column(type="operand_id")
     */
    public OperandId $id;

    /**
     * @Assert\Email
     *
     * @ORM\Column(nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $contractor = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $seller = false;

    public function __construct(OperandId $id)
    {
        $this->id = $id;
    }

    abstract public function __toString(): string;

    public function toId(): OperandId
    {
        return $this->id;
    }

    abstract public function getFullName(): string;

    abstract public function getTelephone(): ?PhoneNumber;

    abstract public function isType(string $type): bool;

    public function isEqual(?self $operand): bool
    {
        return null !== $operand && $operand->toId()->equal($this->id);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function isContractor(): bool
    {
        return $this->contractor;
    }

    public function setContractor(bool $contractor): void
    {
        $this->contractor = $contractor;
    }

    public function isSeller(): bool
    {
        return $this->seller;
    }

    public function setSeller(bool $seller): void
    {
        $this->seller = $seller;
    }
}
