<?php

namespace App\Doctrine\ORM\Type;

use function assert;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\UuidInterface;

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
        if ($value instanceof Identifier) {
            return Type::getType('uuid')->convertToDatabaseValue($value->toUuid(), $platform);
        }

        return Type::getType('uuid')->convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Identifier
    {
        $uuid = Type::getType('uuid')->convertToPHPValue($value, $platform);

        if ($uuid instanceof UuidInterface) {
            $class = $this->class;
            $uuid = new $class($uuid);
        }

        assert($uuid instanceof Identifier);

        return $uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return Type::getType('uuid')->getSQLDeclaration($fieldDeclaration, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
