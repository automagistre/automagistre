<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity
 */
class Wallet
{
    use Identity;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Embedded(class="Money\Currency")
     */
    public Currency $currency;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $useInIncome;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $useInOrder;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $showInLayout;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $defaultInManualTransaction = false;

    /**
     * @var Collection<int, WalletTransaction>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tenant\WalletTransaction", mappedBy="recipient")
     */
    private Collection $transactions;

    public function __construct(
        string $name,
        Currency $currency,
        bool $useInIncome = false,
        bool $useInOrder = false,
        bool $showInLayout = true
    ) {
        $this->name = $name;
        $this->currency = $currency;
        $this->useInIncome = $useInIncome;
        $this->useInOrder = $useInOrder;
        $this->showInLayout = $showInLayout;
        $this->transactions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function debit(Money $money, string $description): void
    {
        $this->transactions[] = new WalletTransaction($this, $description, $money->absolute());
    }

    public function credit(Money $money, string $description): void
    {
        $this->transactions[] = new WalletTransaction($this, $description, $money->negative());
    }
}
