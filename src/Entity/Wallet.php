<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"owner_id", "name"})})
 *
 * @UniqueEntity(fields={"owner", "name"})
 */
class Wallet implements WalletOwner
{
    use Identity;

    /**
     * @var Operand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operand", inversedBy="wallets")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var Currency
     *
     * @ORM\Embedded(class="Money\Currency")
     */
    private $currency;

    public function __construct(Operand $owner, string $name, Currency $currency)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->currency = $currency;

        $owner->addWallet($this);
    }

    public function __toString(): string
    {
        return \sprintf('%s (%s)', $this->owner, $this->name);
    }

    public function isSame(self $wallet): bool
    {
        return $this->id === $wallet->getId();
    }

    public function getOwner(): Operand
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getWallet(): self
    {
        return $this;
    }
}
