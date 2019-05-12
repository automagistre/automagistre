<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Doctrine\Registry;
use App\Entity\Tenant\Wallet;
use App\Enum\Tenant;
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
            new TwigFunction('tenants', [$this, 'tenants']),
        ];
    }

    public function balance(Environment $twig): string
    {
        $em = $this->registry->manager(Wallet::class);

        return $twig->render('admin/layout/balance.html.twig', [
            'wallets' => $em->getRepository(Wallet::class)->findBy(['showInLayout' => true]),
        ]);
    }

    public function tenants(): array
    {
        return Tenant::all();
    }
}
