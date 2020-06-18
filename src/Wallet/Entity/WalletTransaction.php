<?php

declare(strict_types=1);

namespace App\Wallet\Entity;

use App\Entity\Tenant\Transaction;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class WalletTransaction extends Transaction
{
    /**
     * @var Wallet
     *
     * @ORM\ManyToOne(targetEntity=Wallet::class)
     * @ORM\JoinColumn
     */
    private $wallet;

    public function __construct(Wallet $wallet, string $description, Money $money)
    {
        $this->wallet = $wallet;

        parent::__construct($description, $money);
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }
}
