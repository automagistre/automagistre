<?php

declare(strict_types=1);

namespace App\CreatedBy\View;

use App\CreatedBy\Entity\CreatedBy;
use App\Doctrine\Registry;
use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Для легаси кода, в новом коде поля должны приходить вместе с вьюхами.
 */
final class CreatedByExtension extends AbstractExtension
{
    public function __construct(
        private Registry $registry,
        private CacheInterface $cache,
    ) {
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
                    $key = sprintf('created_by_view.%s.%s', $uuid->toString(), http_build_query($options));

                    return $this->cache->get(
                        $key,
                        function (CacheItemInterface $item) use ($twig, $uuid, $options): string {
                            $item->expiresAt(new DateTimeImmutable('+1 month'));

                            $view = $this->registry->get(CreatedBy::class, $uuid);

                            return $twig->render('easy_admin/created_by/created_by_view.html.twig', [
                                'value' => [
                                    'at' => $view->createdAt,
                                    'by' => $view->userId,
                                ],
                                'withUser' => $options['withUser'] ?? true,
                            ]);
                        },
                    );
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
