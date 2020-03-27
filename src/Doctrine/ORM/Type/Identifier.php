<?php

namespace App\Doctrine\ORM\Type;

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
     * @return static
     */
    final public static function generate(): self
    {
        return new static(Uuid::uuid4());
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

    final public function toUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
