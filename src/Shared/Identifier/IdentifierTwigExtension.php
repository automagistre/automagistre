<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use function get_class;
use LogicException;
use function method_exists;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;
use function sprintf;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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
                string $format = null
            ) => $value instanceof Identifier ? $this->formatter->format($value, $format) : $value),
            new TwigFilter(
                'toId',
                static function (object $object): string {
                    if ($object instanceof UuidInterface || $object instanceof Identifier) {
                        return (string) $object;
                    }

                    if (method_exists($object, 'toId')) {
                        return (string) $object->toId();
                    }

                    $refClass = new ReflectionClass($object);
                    if ($refClass->hasProperty('id')) {
                        $refId = $refClass->getProperty('id');

                        if ($refId->isPublic()) {
                            return (string) $refId->getValue($object);
                        }
                    }

                    throw new LogicException(sprintf('Unsupported object %s for toId filter.', get_class($object)));
                },
            ),
            new TwigFilter(
                'toUuid',
                static function (object $object): UuidInterface {
                    $class = get_class($object);

                    if (method_exists($object, 'toId')) {
                        $object = $object->toId();
                    }

                    if ($object instanceof UuidInterface) {
                        return $object;
                    }

                    if ($object instanceof Identifier) {
                        return $object->toUuid();
                    }

                    throw new LogicException(sprintf('Unsupported object %s for toUuid filter.', $class));
                }
            ),
        ];
    }
}
