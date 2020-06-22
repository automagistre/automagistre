<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\Costil;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;

/**
 * @ORM\Entity
 */
class Wallet
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="wallet_id")
     */
    public WalletId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Embedded(class=Currency::class)
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

    public function __construct(
        WalletId $walletId,
        string $name,
        Currency $currency,
        bool $useInIncome = false,
        bool $useInOrder = false,
        bool $showInLayout = true
    ) {
        $this->id = $walletId;
        $this->name = $name;
        $this->currency = $currency;
        $this->useInIncome = $useInIncome;
        $this->useInOrder = $useInOrder;
        $this->showInLayout = $showInLayout;
    }

    public function __toString(): string
    {
        return Costil::$formatter->format($this->id);
    }

    public function toId(): WalletId
    {
        return $this->id;
    }
}
