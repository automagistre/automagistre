<?php

declare(strict_types=1);

namespace App\Income\View;

use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
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

        return $formatter->format($incomePart->getIncome()->toId());
    }
}
