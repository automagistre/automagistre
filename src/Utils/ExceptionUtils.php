<?php

declare(strict_types=1);

namespace App\Utils;

use Doctrine\Common\Util\ClassUtils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExceptionUtils
{
    /**
     * @param mixed $current
     */
    public static function invalidType(string $variable, string $type, $current): string
    {
        $currentType = \is_object($current) ? ClassUtils::getClass($current) : \gettype($current);

        return \sprintf('Variable "%s", must be type of "%s", but "%s" given.', $variable, $type, $currentType);
    }
}
