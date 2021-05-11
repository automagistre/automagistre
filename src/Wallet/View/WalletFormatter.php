<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletId;
use Premier\Identifier\Identifier;

final class WalletFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $wallet = $this->registry->get(Wallet::class, $identifier);

        return $wallet->name;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return WalletId::class;
    }
}
