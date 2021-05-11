<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Shared\Doctrine\Registry;
use GraphQL\Deferred;
use function array_keys;
use function array_map;
use function assert;
use function is_array;

final class Buffer
{
    /**
     * @var array<class-string, array<string, bool>>
     */
    private array $buffer = [];

    /**
     * @psalm-var array<class-string, array<string, object>>
     */
    private array $loaded = [];

    public function __construct(private Registry $registry)
    {
    }

    /**
     * @psalm-param class-string $class
     *
     * @param string|string[] $ids
     */
    public function add(string $class, $ids): Deferred
    {
        foreach ((array) $ids as $id) {
            $this->buffer[$class][$id] = true;
        }

        $executor = is_array($ids)
            ? fn (): array => array_map(fn (string $id): object => $this->get($class, $id), $ids)
            : fn (): object => $this->get($class, $ids);

        return new Deferred($executor);
    }

    /**
     * @psalm-param class-string $class
     */
    private function load(string $class): void
    {
        $ids = $this->buffer[$class] ?? [];
        unset($this->buffer[$class]);

        $result = $this->registry->manager()
            ->createQueryBuilder()
            ->select('t')
            ->from($class, 't', 't.id')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', array_keys($ids))
            ->getQuery()
            ->getResult()
        ;

        foreach ($result as $id => $value) {
            $this->loaded[$class][$id] = (object) $value;
        }
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     */
    private function get(string $class, string $id): object
    {
        if (null === ($this->loaded[$class][$id] ?? null)) {
            $this->load($class);
        }

        $object = $this->loaded[$class][$id];

        assert($object instanceof $class);

        return $object;
    }
}
