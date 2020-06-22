<?php

declare(strict_types=1);

namespace App\Customer\View;

use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\CustomerTransactionView;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class CustomerTransactionFormatter implements IdentifierFormatterInterface
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
        $view = $this->registry->getBy(CustomerTransactionView::class, ['id' => $identifier]);

        if (!$view->source->isPayroll()) {
            return $formatter->format($view->toSourceIdentifier());
        }

        return $formatter->format($view->operandId);
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return CustomerTransactionId::class;
    }
}
