<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Entity\WalletTransactionView;
use Premier\Identifier\Identifier;

final class WalletTransactionFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->getBy(WalletTransactionView::class, ['id' => $identifier]);

        if (!$view->source->isPayroll()) {
            return $formatter->format($view->toSourceIdentifier());
        }

        return $formatter->format($view->walletId);
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return WalletTransactionId::class;
    }
}
