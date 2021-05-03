<?php

declare(strict_types=1);

namespace App\Expense\View;

use App\Expense\Entity\Expense;
use App\Expense\Entity\ExpenseId;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class ExpenseFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
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
