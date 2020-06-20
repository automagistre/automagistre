<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Entity\WalletTransactionView;

final class WalletTransactionFormatter implements IdentifierFormatterInterface
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
