<?php

namespace App\Shared\Identifier;

use function assert;
use function call_user_func;
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

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Identifier
    {
        $uuid = Type::getType('uuid')->convertToPHPValue($value, $platform);

        if (!$uuid instanceof UuidInterface) {
            return null;
        }

        /** @var callable $callable */
        $callable = $this->class.'::fromUuid';
        $identifier = call_user_func($callable, $uuid);

        assert($identifier instanceof Identifier);

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($fieldDeclaration);
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
