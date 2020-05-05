<?php

declare(strict_types=1);

namespace App\Infrastructure\Identifier;

use App\Doctrine\ORM\Type\Identifier;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class IdentifierTwigExtension extends AbstractExtension
{
    private IdentifierFormatter $formatter;

    public function __construct(IdentifierFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('display_name', fn (
                $value,
                string $format = null
            ) => $value instanceof Identifier ? $this->formatter->format($value, $format) : $value),
        ];
    }
}
