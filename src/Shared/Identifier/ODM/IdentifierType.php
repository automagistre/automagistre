<?php

namespace App\Shared\Identifier\ODM;

use App\Shared\Identifier\Identifier;
use function assert;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ODM\MongoDB\Types\Type;
use function sprintf;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class IdentifierType extends Type
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
    public function convertToPHPValue($value): ?Identifier
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
    public function convertToDatabaseValue($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!$value instanceof Identifier) {
            ConversionException::conversionFailed($value, $this->name);
        }

        return $value->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function closureToPHP(): string
    {
        return sprintf(
            'if (null === $value) {
                $identifier = null;
            } elseif ($value instanceof %s) {
                $identifier = $value;
            } else {
                try {
                    $identifier = %s::fromString($value);
                } catch (InvalidArgumentException $e) {
                    throw ConversionException::conversionFailed($value, \'%s\');
                }
            }

            $return = $identifier;',
            $this->class,
            $this->class,
            $this->name,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function closureToMongo(): string
    {
        return sprintf(
            'if (null === $value) {
                $mongo = null;
            } elseif ($value instanceof %s) {
                $mongo = $value->toString();
            } else {
                throw ConversionException::conversionFailed($value, \'%s\');
            }

            $return = $mongo;',
            $this->class,
            $this->name,
        );
    }
}
