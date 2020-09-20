<?php

namespace App\Shared\Identifier\ORM;

use App\Shared\Identifier\Identifier;
use function array_map;
use function assert;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use function is_array;
use function json_decode;
use function json_encode;
use JsonException;

final class IdentifierArrayType extends Type
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
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw ConversionException::conversionFailed($value, $this->name);
        }

        $value = array_map(static fn (Identifier $identifier): string => $identifier->toString(), $value);

        try {
            /** @var string $encoded */
            $encoded = json_encode($value, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage());
        }

        return $encoded;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): array
    {
        if (null === $value) {
            return [];
        }

        try {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }

        /** @var callable $callable */
        $callable = $this->class.'::fromString';

        return array_map(static fn (string $id): Identifier => $callable($id), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
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
        return !$platform->hasNativeJsonType();
    }
}
