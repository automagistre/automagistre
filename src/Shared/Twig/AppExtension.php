<?php

declare(strict_types=1);

namespace App\Shared\Twig;

use DateTimeImmutable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function assert;
use function is_int;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AppExtension extends AbstractExtension
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
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
