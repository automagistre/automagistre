<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Landlord\Tenant;
use App\Entity\Tenant\Wallet;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LayoutExtension extends AbstractExtension
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
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
        $em = $this->registry->getManagerForClass(Wallet::class);

        return $twig->render('admin/layout/balance.html.twig', [
            'wallets' => $em->getRepository(Wallet::class)->findBy(['showInLayout' => true]),
        ]);
    }

    public function tenants(string $identifier): array
    {
        $repository = $this->registry->getManagerForClass(Tenant::class)
            ->getRepository(Tenant::class);

        return [
            'all' => $repository->findAll(),
            'current' => $repository->findOneBy(['identifier' => $identifier]),
        ];
    }
}
