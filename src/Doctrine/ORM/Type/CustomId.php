<?php

namespace App\Doctrine\ORM\Type;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class CustomId
{
    private UuidInterface $uuid;

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    final public function __toString(): string
    {
        return $this->uuid->toString();
    }

    final public static function generate(): self
    {
        return new static(Uuid::uuid4());
    }

    final public static function fromString(string $uuid): self
    {
        return new static(Uuid::fromString($uuid));
    }

    final public static function fromUuid(UuidInterface $uuid): self
    {
        return new static($uuid);
    }

    final public function toUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
