<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class WalletTransaction extends Transaction
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\Wallet", inversedBy="transactions")
     * @ORM\JoinColumn
     */
    private Wallet $recipient;

    public function __construct(Wallet $wallet, string $description, Money $money)
    {
        $this->recipient = $wallet;

        parent::__construct($description, $money);
    }

    public function getRecipient(): Wallet
    {
        return $this->recipient;
    }
}
