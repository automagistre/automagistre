<?php

declare(strict_types=1);

namespace App\Doctrine;

use function assert;
use function class_exists;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use function get_class;
use function is_object;
use LogicException;
use function str_replace;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Registry
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @psalm-param class-string $class
     */
    public function manager(string $class): EntityManagerInterface
    {
        $em = $this->managerOrNull($class);

        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManager expected');
        }

        return $em;
    }

    public function managerOrNull(string $class): ?EntityManagerInterface
    {
        $em = $this->registry->getManagerForClass($this->class($class));
        if (null === $em) {
            return null;
        }

        assert($em instanceof EntityManagerInterface);

        return $em;
    }

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return EntityRepository<T>
     */
    public function repository(string $class): EntityRepository
    {
        return $this->manager($class)->getRepository($class);
    }

    /**
     * @param object|string $entity
     */
    public function class($entity): string
    {
        return is_object($entity)
                ? str_replace('Proxies\\__CG__\\', '', get_class($entity))
                : $entity;
    }

    /**
     * @param object|string $entity
     */
    public function classMetaData($entity): ClassMetadataInfo
    {
        $class = $this->class($entity);

        assert(class_exists($class));

        return $this->manager($class)->getClassMetadata($class);
    }

    /**
     * @deprecated Must be removed with EventsListener
     *
     * @param mixed $entity
     */
    public function isEntity($entity): bool
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        if (!class_exists((string) $entity)) {
            return false;
        }

        return null !== $this->managerOrNull($entity);
    }
}
