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
                function (Environment $twig, mixed $entity, array $options = []): string {
                    $key = sprintf('created_by_view.%s.%s', $entity->toId()->toString(), http_build_query($options));

                    return $this->cache->get(
                        $key,
                        function (CacheItemInterface $item) use ($twig, $entity, $options): string {
                            return $twig->render('easy_admin/created_by/created_by_view.html.twig', [
                                'value' => [
                                    'at' => $entity->createdAt,
                                    'by' => $entity->createdBy,
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
                function (mixed $entity): DateTimeImmutable {
                    return $entity->createdAt;
                },
            ),
        ];
    }
}
