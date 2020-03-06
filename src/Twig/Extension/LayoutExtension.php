<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Doctrine\Registry;
use App\Entity\Tenant\Wallet;
use App\Wallet\BalanceProvider;
use function array_map;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LayoutExtension extends AbstractExtension
{
    private Registry $registry;

    /** @var BalanceProvider */
    private BalanceProvider $balanceProvider;

    public function __construct(Registry $registry, BalanceProvider $balanceProvider)
    {
        $this->registry = $registry;
        $this->balanceProvider = $balanceProvider;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'layout_balance',
                [$this, 'balance'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]),
        ];
    }

    public function balance(Environment $twig): string
    {
        $em = $this->registry->manager(Wallet::class);

        return $twig->render('admin/layout/balance.html.twig', [
            'wallets' => array_map(fn (Wallet $wallet) => [
                'id' => $wallet->getId(),
                'name' => $wallet->name,
                'balance' => $this->balanceProvider->balance($wallet),
            ], $em->getRepository(Wallet::class)->findBy(['showInLayout' => true])),
        ]);
    }
}
