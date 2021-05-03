<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletId;

final class WalletFormatter implements IdentifierFormatterInterface
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
