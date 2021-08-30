<?php

declare(strict_types=1);

namespace App\Twig;

use DateTimeImmutable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function assert;
use function is_int;
use function iterator_to_array;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AppExtension extends AbstractExtension
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('instanceOf', [$this, 'doInstanceOf']),
            new TwigFunction('build_time', [$this, 'buildTime']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('iterator_to_array', fn (Traversable $iterator) => iterator_to_array($iterator)),
        ];
    }

    public function doInstanceOf(object $object, string $class): bool
    {
        return $object instanceof $class;
    }

    public function buildTime(): DateTimeImmutable
    {
        $timestamp = $this->parameterBag->get('container.build_time');

        assert(is_int($timestamp));

        return new DateTimeImmutable('@'.$timestamp);
    }
}
