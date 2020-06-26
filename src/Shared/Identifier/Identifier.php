<?php

namespace App\Shared\Identifier;

use function assert;
use function is_string;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @psalm-immutable
 */
abstract class Identifier
{
    private UuidInterface $uuid;

    /**
     * @final
     */
    private function __construct(UuidInterface $uuid)
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
    final public static function fromString(string $uuid): self
    {
        return new static(Uuid::fromString($uuid));
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
