<?php

namespace App\Shared\Enum;

use function assert;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ODM\MongoDB\Types\Type;
use const FILTER_VALIDATE_INT;
use function filter_var;
use Premier\Enum\Enum;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class EnumODMType extends Type
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $property;

    /**
     * @psalm-param class-string<Enum> $class
     */
    public static function register(string $class, string $name, string $property = 'id'): void
    {
        Type::addType($name, self::class);
        $type = Type::getType($name);
        assert($type instanceof self);

        $type->class = $class;
        $type->name = $name;
        $type->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value): ?Enum
    {
        if (null === $value) {
            return null;
        }

        $class = $this->class;
        if ($value instanceof $class) {
            assert($value instanceof Enum);

            return $value;
        }

        if ('id' === $this->property) {
            if (false === $id = filter_var($value, FILTER_VALIDATE_INT)) {
                throw ConversionException::conversionFailed($value, $this->name);
            }

            $value = $id;
        }

        /** @var callable $callable */
        $callable = [$class, 'from'];
        $enum = $callable($this->property, $value);

        if (!$enum instanceof $class) {
            throw ConversionException::conversionFailed($value, $this->name);
        }

        assert($enum instanceof Enum);

        return $enum;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value)
    {
        if (null === $value) {
            return null;
        }

        $class = $this->class;
        if (!$value instanceof $class) {
            throw ConversionException::conversionFailed($value, $this->name);
        }

        assert($value instanceof Enum);

        return $value->to($this->property);
    }
}
