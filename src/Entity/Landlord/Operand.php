<?php

declare(strict_types=1);

namespace App\Entity\Landlord;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\Uuid;
use App\Entity\Tenant\OperandTransaction;
use App\Entity\Transactional;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({"1": "Person", "2": "Organization"})
 */
abstract class Operand implements Transactional
{
    use Identity;
    use Uuid;

    /**
     * @var string
     *
     * @Assert\Email
     *
     * @ORM\Column(nullable=true)
     */
    private $email;

    /**
     * @var bool Подрядчик
     *
     * @ORM\Column(type="boolean")
     */
    private $contractor = false;

    /**
     * @var bool Поставщик
     *
     * @ORM\Column(type="boolean")
     */
    private $seller = false;

    public function __construct()
    {
        $this->generateUuid();
    }

    abstract public function __toString(): string;

    abstract public function getFullName(): string;

    abstract public function getTelephone(): ?PhoneNumber;

    public function getTransactionClass(): string
    {
        return OperandTransaction::class;
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
