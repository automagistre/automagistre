<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\Tenant\Entity\TenantEntity;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="wallet_view")
 *
 * @psalm-suppress MissingConstructor
 */
class WalletView extends TenantEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="wallet_id")
     */
    public WalletId $id;

    /**
     * @ORM\Column
     */
    public string $name;

    /**
     * @ORM\Column(type="money")
     */
    public Money $balance;

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

    public function toId(): WalletId
    {
        return $this->id;
    }
}
