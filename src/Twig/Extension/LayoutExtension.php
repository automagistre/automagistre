<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Doctrine\Registry;
use App\Entity\Tenant\Wallet;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LayoutExtension extends AbstractExtension
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
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
            'wallets' => $em->getRepository(Wallet::class)->findBy(['showInLayout' => true]),
        ]);
    }
}
