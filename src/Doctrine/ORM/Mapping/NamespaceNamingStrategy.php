<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use LogicException;
use function explode;
use function implode;
use function is_string;
use function preg_replace;
use function str_replace;
use function strpos;
use function strtolower;
use function strtoupper;
use const CASE_LOWER;
use const CASE_UPPER;

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

        $namespace = $this->underscore(str_replace(self::SEARCH, '', $className));

        return implode('_', explode('\\', $namespace));
    }

    private function underscore(string $string, int $case = CASE_LOWER): string
    {
        $string = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string);

        if (!is_string($string)) {
            throw new LogicException('String expected.');
        }

        if (CASE_UPPER === $case) {
            return strtoupper($string);
        }

        return strtolower($string);
    }
}
