<?php

declare(strict_types=1);

namespace App\Wallet\View;

use App\Doctrine\Registry;
use App\Wallet\Entity\WalletView;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LayoutExtension extends AbstractExtension
{
    public function __construct(private Registry $registry)
    {
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
                ],
            ),
        ];
    }

    public function balance(Environment $twig): string
    {
        return $twig->render('admin/layout/balance.html.twig', [
            'wallets' => $this->registry->findBy(WalletView::class, ['showInLayout' => true]),
        ]);
    }
}
