<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping;

use App\Utils\StringUtils;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use function explode;
use function implode;
use function str_replace;
use function strpos;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NamespaceNamingStrategy extends UnderscoreNamingStrategy
{
    private const PREFIX = 'App\\Entity';

    private const SEARCH = [self::PREFIX.'\\Landlord\\', self::PREFIX.'\\Tenant\\', self::PREFIX.'\\'];

    public function __construct()
    {
        parent::__construct(CASE_LOWER, true);
    }

    /**
     * @param string $className
     */
    public function classToTableName($className): string
    {
        if (0 !== strpos($className, self::PREFIX)) {
            return parent::classToTableName($className);
        }

        $namespace = StringUtils::underscore(str_replace(self::SEARCH, '', $className));

        return implode('_', explode('\\', $namespace));
    }
}
