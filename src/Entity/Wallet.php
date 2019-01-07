<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;

/**
 * @ORM\Entity
 */
class Wallet implements Transactional
{
    use Identity;

    /**
     * @var string
     *
     * @ORM\Column
     */
    public $name;

    /**
     * @var Currency
     *
     * @ORM\Embedded(class="Money\Currency")
     */
    public $currency;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $useInIncome = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $useInOrder = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    public $showInLayout = true;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getTransactionClass(): string
    {
        return WalletTransaction::class;
    }
}
