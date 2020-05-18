<?php

declare(strict_types=1);

namespace App\Vehicle\Infrastructure;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Vehicle\Domain\VehicleId;
use function sprintf;

final class VehicleFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->view($identifier);

        $text = sprintf(
            '%s %s',
            $formatter->format($view['manufacturerId']),
            $view['name'],
        );

        $case = $view['caseName'] ?? null;
        if (null !== $case) {
            $text .= sprintf(' - %s', $case);
        }

        $from = $view['yearFrom'] ?? null;
        $till = $view['yearTill'] ?? null;

        if ('long' === $format && (null !== $from || null !== $till)) {
            $text .= sprintf(' (%s - %s)', $from ?? '...', $till ?? '...');
        }

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return VehicleId::class;
    }
}
