<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping;

use App\Utils\StringUtils;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NamespaceNamingStrategy implements NamingStrategy
{
    const PREFIX = 'App\\Entity';

    /**
     * @var UnderscoreNamingStrategy
     */
    private $strategy;

    public function __construct(UnderscoreNamingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function classToTableName($className): string
    {
        if (0 !== strpos($className, self::PREFIX)) {
            return $this->strategy->classToTableName($className);
        }

        return implode('_', explode('\\', StringUtils::underscore(str_replace(self::PREFIX.'\\', '', $className))));
    }

    public function propertyToColumnName($propertyName, $className = null): string
    {
        return $this->strategy->propertyToColumnName($propertyName, $className);
    }

    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null): string
    {
        return $this->strategy->embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className, $embeddedClassName);
    }

    public function referenceColumnName(): string
    {
        return $this->strategy->referenceColumnName();
    }

    public function joinColumnName($propertyName): string
    {
        return $this->strategy->joinColumnName($propertyName);
    }

    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null): string
    {
        return $this->strategy->joinTableName($sourceEntity, $targetEntity, $propertyName);
    }

    public function joinKeyColumnName($entityName, $referencedColumnName = null): string
    {
        return $this->strategy->joinKeyColumnName($entityName, $referencedColumnName);
    }
}
