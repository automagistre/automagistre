<?php

declare(strict_types=1);

namespace GuzzleHttp\Command;

use ArrayIterator;
use function array_key_exists;
use function count;

/**
 * Basic collection behavior for Command and Result objects.
 *
 * The methods in the class are primarily for implementing the ArrayAccess,
 * Countable, and IteratorAggregate interfaces.
 */
trait HasDataTrait
{
    /** @var array Data stored in the collection. */
    protected $data;

    public function __toString()
    {
        return (string) print_r($this, true);
    }

    public function __debugInfo()
    {
        return $this->data;
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset): mixed
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    public function toArray()
    {
        return $this->data;
    }
}
