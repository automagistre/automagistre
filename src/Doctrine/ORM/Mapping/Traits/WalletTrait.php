<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use App\Entity\Wallet;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait WalletTrait
{
    /**
     * @var Wallet
     *
     * @ORM\OneToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $wallet;

    public function setWallet(Wallet $wallet): void
    {
        $this->wallet = $wallet;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }
}
