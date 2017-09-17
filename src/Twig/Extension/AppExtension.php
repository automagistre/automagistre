<?php

declare(strict_types=1);

namespace App\Twig\Extension;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AppExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('instanceOf', [$this, 'doInstanceOf']),
            new \Twig_SimpleFunction('build', [$this, 'build']),
            new \Twig_SimpleFunction('build_time', [$this, 'buildTime']),
        ];
    }

    public function doInstanceOf($object, $class): bool
    {
        return $object instanceof $class;
    }

    public function build(): string
    {
        return getenv('APP_BUILD');
    }

    public function buildTime(): \DateTimeImmutable
    {
        if ($time = getenv('APP_BUILD_TIME')) {
            return \DateTimeImmutable::createFromFormat(DATE_RFC3339, $time);
        }

        return new \DateTimeImmutable();
    }
}
