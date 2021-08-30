<?php

declare(strict_types=1);

namespace App\Income\View;

use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use Premier\Identifier\Identifier;

final class IncomePartFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return IncomePartId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        /** @var IncomePart $incomePart */
        $incomePart = $this->registry->findOneBy(IncomePart::class, ['id' => $identifier]);

        return $formatter->format($incomePart->income->toId());
    }
}
