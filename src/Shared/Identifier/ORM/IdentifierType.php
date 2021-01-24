<?php

declare(strict_types=1);

namespace App\Shared\Identifier\ORM;

use App\Shared\Identifier\Identifier;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function assert;
use function is_string;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class IdentifierType extends Type
{
    public string $name;

    /** @var class-string<Identifier> */
    public string $class;

    /**
     * @param class-string<Identifier> $class
     */
    public static function register(string $name, string $class): void
    {
        Type::addType($name, self::class);
        $type = Type::getType($name);
        assert($type instanceof self);

        $type->name = $name;
        $type->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_string($value) && Uuid::isValid($value)) {
            return $value;
        }

        if ($value instanceof Identifier || $value instanceof UuidInterface) {
            return $value->toString();
        }

        throw ConversionException::conversionFailed($value, $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Identifier
    {
        if (null === $value || '' === $value) {
            return null;
        }

        /** @var callable $callable */
        $callable = $this->class.'::fromString';
        $identifier = $callable($value);

        assert($identifier instanceof Identifier);

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
