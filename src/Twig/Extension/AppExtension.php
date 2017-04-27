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
        ];
    }

    public function doInstanceOf($object, $class): bool
    {
        return $object instanceof $class;
    }
}
