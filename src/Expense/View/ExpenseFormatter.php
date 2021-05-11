<?php

declare(strict_types=1);

namespace App\Expense\View;

use App\Expense\Entity\Expense;
use App\Expense\Entity\ExpenseId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use Premier\Identifier\Identifier;

final class ExpenseFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $expense = $this->registry->get(Expense::class, $identifier);

        return $expense->name;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return ExpenseId::class;
    }
}
