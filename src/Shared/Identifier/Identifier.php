<?php

namespace App\Shared\Identifier;

use function assert;
use function get_debug_type;
use function is_string;
use JsonSerializable;
use LogicException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
abstract class Identifier implements JsonSerializable
{
    private UuidInterface $uuid;

    final private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    final public function __toString(): string
    {
        return $this->toString();
    }

    final public function toString(): string
    {
        return $this->uuid->toString();
    }

    /**
     * {@inheritDoc}
     */
    final public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     *
     * @param UuidInterface|string $uuid
     */
    final public static function fromClass(string $class, $uuid)
    {
        /** @var callable $callable */
        $callable = $class.'::'.(is_string($uuid) ? 'fromString' : 'fromUuid');
        $identifier = $callable($uuid);

        assert($identifier instanceof $class);

        return $identifier;
    }

    /**
     * @psalm-mutation-free
     */
    final public static function same(?self $left, ?self $right): bool
    {
        return (null === $left && null === $right)
            || (null === $left ? null : $left->toString()) === (null === $right ? null : $right->toString());
    }

    /**
     * @return static
     */
    final public static function generate(): self
    {
        return new static(Uuid::uuid6());
    }

    /**
     * @return static
     */
    public static function fromUuidOrNull(?UuidInterface $uuid): ?self
    {
        return null === $uuid ? null : self::fromUuid($uuid);
    }

    /**
     * @return static
     */
    final public static function fromString(string $uuid): self
    {
        return new static(Uuid::fromString($uuid));
    }

    /**
     * @param mixed $any
     *
     * @return static
     */
    public static function fromAny($any): self
    {
        if ($any instanceof static) {
            return $any;
        }

        if ($any instanceof UuidInterface) {
            return static::fromUuid($any);
        }

        if (is_string($any)) {
            return static::fromString($any);
        }

        throw new LogicException('Unexpected any: '.get_debug_type($any));
    }

    /**
     * @return static
     */
    final public static function fromUuid(UuidInterface $uuid): self
    {
        return new static($uuid);
    }

    public static function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    final public function toUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function equal(?self $identifier): bool
    {
        return null !== $identifier && $identifier->toString() === $this->toString();
    }
}
