<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Doctrine\ORM\Mapping\Traits\WalletTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="integer")
 * @ORM\DiscriminatorMap({"1": "Person", "2": "Organization"})
 */
abstract class Operand implements WalletOwner
{
    use Identity;
    use WalletTrait {
        setWallet as private doSetWallet;
    }

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

    /**
     * @var Wallet[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Wallet", mappedBy="owner")
     * @ORM\OrderBy({"id": "ASC"})
     */
    private $wallets;

    public function __construct()
    {
        $this->wallets = new ArrayCollection();
    }

    abstract public function __toString(): string;

    public function addWallet(Wallet $account): void
    {
        $this->wallets[] = $account;
    }

    /**
     * @return Wallet[]
     */
    public function getWallets(): array
    {
        return $this->wallets->toArray();
    }

    public function setWallet(Wallet $wallet): void
    {
        if (!$this->wallets->contains($wallet)) {
            throw new LogicException('Default wallet must be in wallets of this Operand');
        }

        $this->doSetWallet($wallet);
    }

    abstract public function getFullName(): string;

    abstract public function getTelephone(): ?PhoneNumber;

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
