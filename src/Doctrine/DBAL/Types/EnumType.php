<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Premier\Enum\Enum;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class EnumType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Enum
    {
        if (null === $value) {
            return null;
        }

        $class = $this->getClass();
        if ($value instanceof Enum && $value instanceof $class) {
            return $value;
        }

        if (false === $id = \filter_var($value, FILTER_VALIDATE_INT)) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        if (false === \class_exists($class)) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        /** @var callable $func */
        $func = [$class, 'create'];
        $enum = $func($id);

        if (!$enum instanceof Enum) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $enum;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if (null === $value) {
            return null;
        }

        $class = $this->getClass();

        if ($value instanceof Enum && $value instanceof $class) {
            return $value->getId();
        }

        if (false === $id = \filter_var($value, FILTER_VALIDATE_INT)) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    abstract protected function getClass(): string;
}
