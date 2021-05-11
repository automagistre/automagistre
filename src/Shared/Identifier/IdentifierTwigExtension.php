<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use LogicException;
use Premier\Identifier\Identifier;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function get_class;
use function is_array;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;

final class IdentifierTwigExtension extends AbstractExtension
{
    public function __construct(private IdentifierFormatter $formatter)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_identifier', fn ($mixed) => $mixed instanceof Identifier),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('display_name', fn (
                $value,
                string $format = null,
            ) => $value instanceof Identifier ? $this->formatter->format($value, $format) : $value),
            new TwigFilter(
                'toId',
                /** @param mixed $value */
                static function ($value): string {
                    if (is_array($value)) {
                        $value = $value['id'] ?? null;
                    }

                    if (is_string($value) && Uuid::isValid($value)) {
                        return $value;
                    }

                    if (!is_object($value)) {
                        throw new LogicException('Object required.');
                    }

                    if ($value instanceof UuidInterface || $value instanceof Identifier) {
                        return (string) $value;
                    }

                    if (method_exists($value, 'toId')) {
                        return (string) $value->toId();
                    }

                    $refClass = new ReflectionClass($value);

                    if ($refClass->hasProperty('id')) {
                        $refId = $refClass->getProperty('id');

                        if ($refId->isPublic()) {
                            return (string) $refId->getValue($value);
                        }
                    }

                    throw new LogicException(sprintf('Unsupported object %s for toId filter.', get_class($value)));
                },
            ),
            new TwigFilter(
                'toUuid',
                /** @param mixed $value */
                static function ($value): UuidInterface {
                    if (is_array($value)) {
                        $value = $value['id'] ?? null;
                    }

                    if (is_string($value) && Uuid::isValid($value)) {
                        return Uuid::fromString($value);
                    }

                    if (!is_object($value)) {
                        throw new LogicException('Object required.');
                    }

                    $class = get_class($value);

                    if (method_exists($value, 'toId')) {
                        $value = $value->toId();
                    }

                    if ($value instanceof UuidInterface) {
                        return $value;
                    }

                    if ($value instanceof Identifier) {
                        return $value->toUuid();
                    }

                    throw new LogicException(sprintf('Unsupported object %s for toUuid filter.', $class));
                },
            ),
        ];
    }
}
