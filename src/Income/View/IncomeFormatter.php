<?php

declare(strict_types=1);

namespace App\Income\View;

use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class IncomeFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return IncomeId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $income = $this->registry->get(Income::class, $identifier);

        return $formatter->format($income->getSupplierId());
    }
}
