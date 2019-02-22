<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Salary
{
    use Identity;

    /**
     * @var OperandTransaction
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\OperandTransaction")
     */
    private $income;

    /**
     * @var WalletTransaction
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant\WalletTransaction")
     */
    private $outcome;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $description;

    public function __construct(OperandTransaction $income, WalletTransaction $outcome, string $description = null)
    {
        $this->income = $income;
        $this->outcome = $outcome;
        $this->description = $description;
    }
}
