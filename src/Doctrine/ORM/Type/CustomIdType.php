<?php

namespace App\Doctrine\ORM\Type;

use function assert;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

final class CustomIdType extends UuidType
{
    public string $name;

    /**
     * @var string class-string<CustomId>
     */
    public string $class;

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
        if ($value instanceof CustomId) {
            return parent::convertToDatabaseValue($value->toUuid(), $platform);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UuidInterface
    {
        $uuid = parent::convertToPHPValue($value, $platform);

        if ($uuid instanceof UuidInterface) {
            $class = $this->class;
            $uuid = new $class($uuid);
        }

        return $uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
