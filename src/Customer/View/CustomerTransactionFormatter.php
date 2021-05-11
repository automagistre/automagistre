<?php

declare(strict_types=1);

namespace App\Customer\View;

use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\CustomerTransactionView;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use Premier\Identifier\Identifier;

final class CustomerTransactionFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
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
