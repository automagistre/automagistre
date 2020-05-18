<?php

declare(strict_types=1);

namespace App\Income\View;

use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Income\Entity\IncomeId;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use function sprintf;

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
        $view = $this->registry->view($identifier);

        return sprintf('Приход от %s', $formatter->format($view['supplierId']));
    }
}
