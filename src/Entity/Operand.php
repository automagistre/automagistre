<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({"1": "Person", "2": "Organization"})
 */
abstract class Operand
{
    use Identity;

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

    /**
     * @var Account[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Account", mappedBy="owner")
     */
    private $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    abstract public function __toString(): string;

    public function addAccount(Account $account): void
    {
        $this->accounts[] = $account;
    }

    public function getAccounts(): array
    {
        return $this->accounts->toArray();
    }

    abstract public function getFullName(): string;

    abstract public function getTelephone(): ?PhoneNumber;

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
