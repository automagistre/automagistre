<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Grachevko\Enum\Enum;

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

        return new $class($id);
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

    abstract protected function getClass(): string;
}
