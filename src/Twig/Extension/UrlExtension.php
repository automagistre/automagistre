<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Utils\UrlUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UrlExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('add_query', [UrlUtils::class, 'addQuery']),
            new TwigFilter('remove_query', [UrlUtils::class, 'removeQuery']),
        ];
    }
}
