<?php

declare(strict_types=1);

namespace App\Entity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Wallet")
     * @ORM\JoinColumn
     */
    private $recipient;

    public function __construct(Wallet $wallet, string $description, Money $money, Money $subtotal)
    {
        $this->recipient = $wallet;

        parent::__construct($description, $money, $subtotal);
    }

    public function getRecipient(): Wallet
    {
        return $this->recipient;
    }
}
