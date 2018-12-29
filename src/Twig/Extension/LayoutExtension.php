<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Costil;
use App\Entity\Operand;
use App\Manager\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LayoutExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaymentManager
     */
    private $paymentManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        EntityManagerInterface $em,
        PaymentManager $paymentManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->em = $em;
        $this->paymentManager = $paymentManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('balance', [$this, 'balance'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function balance(Environment $twig): string
    {
        $em = $this->em;
        $paymentManager = $this->paymentManager;

        /** @var Operand $cassa */
        $cassa = $em->getReference(Operand::class, Costil::CASHBOX);

        return $twig->render('admin/layout/balance.html.twig', [
            'url' => $this->urlGenerator->generate('easyadmin', [
                'entity' => 'Operand',
                'action' => 'show',
                'id' => $cassa->getId(),
            ]),
            'money' => $paymentManager->balance($cassa),
        ]);
    }
}
