<?php

declare(strict_types=1);

namespace App\Income\View;

use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class IncomePartFormatter implements IdentifierFormatterInterface
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
        return IncomePartId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        /** @var IncomePart $incomePart */
        $incomePart = $this->registry->findBy(IncomePart::class, ['uuid' => $identifier]);

        return $formatter->format($incomePart->getIncome()->toId());
    }
}
