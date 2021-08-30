<?php

declare(strict_types=1);

namespace App\CreatedBy\View;

use App\CreatedBy\Entity\CreatedBy;
use App\CreatedBy\Entity\CreatedByView;
use App\Doctrine\Registry;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Для легаси кода, в новом коде поля должны приходить вместе с вьюхами.
 */
final class CreatedByExtension extends AbstractExtension
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'created_by_view',
                function (Environment $twig, UuidInterface $uuid, array $options = []): string {
                    $view = $this->registry->get(CreatedByView::class, $uuid);

                    return $twig->render('easy_admin/created_by/created_by_view.html.twig', [
                        'value' => $view,
                        'withUser' => $options['withUser'] ?? true,
                    ]);
                },
                [
                    'needs_environment' => true,
                    'is_safe' => ['html' => true],
                ],
            ),
            new TwigFunction(
                'created_at',
                function (UuidInterface $uuid): DateTimeImmutable {
                    return $this->registry->get(CreatedBy::class, $uuid)->createdAt;
                },
            ),
        ];
    }
}
