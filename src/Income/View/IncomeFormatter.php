<?php

declare(strict_types=1);

namespace App\Income\View;

use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use Premier\Identifier\Identifier;

final class IncomeFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
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
