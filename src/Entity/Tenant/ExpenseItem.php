<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Shared\Doctrine\ORM\Mapping\Traits\CreatedByRelation;
use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Entity
 */
class ExpenseItem
{
    use Identity;
    use CreatedAt;
    use CreatedByRelation;

    /**
     * @var Expense
     *
     * @ORM\ManyToOne(targetEntity="Expense")
     */
    private $expense;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class)
     */
    private $amount;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $description;

    public function __construct(Expense $expense, Money $amount, string $description = null)
    {
        $this->expense = $expense;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getExpense(): Expense
    {
        return $this->expense;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
