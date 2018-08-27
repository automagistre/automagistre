<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping;

use App\Utils\StringUtils;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NamespaceNamingStrategy extends UnderscoreNamingStrategy
{
    private const PREFIX = 'App\\Entity';

    /**
     * @param string $className
     */
    public function classToTableName($className): string
    {
        if (0 !== \strpos($className, self::PREFIX)) {
            return parent::classToTableName($className);
        }

        return \implode('_', \explode('\\', StringUtils::underscore(\str_replace(self::PREFIX.'\\', '', $className))));
    }
}
