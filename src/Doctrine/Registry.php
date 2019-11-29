<?php

declare(strict_types=1);

namespace App\Doctrine;

use function assert;
use function class_exists;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use function get_class;
use function is_object;
use LogicException;

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
     * @param object|string $entity
     */
    public function manager($entity): EntityManagerInterface
    {
        $em = $this->managerOrNull($entity);

        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManager expected');
        }

        return $em;
    }

    /**
     * @param object|string $entity
     */
    public function managerOrNull($entity): ?EntityManagerInterface
    {
        $em = $this->registry->getManagerForClass($this->entityToString($entity));
        if (null === $em) {
            return null;
        }

        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManagerInterface expected.');
        }

        return $em;
    }

    /**
     * @param object|string $entity
     */
    public function repository($entity): EntityRepository
    {
        $class = $this->entityToString($entity);

        assert(class_exists($class));

        return $this->manager($class)->getRepository($class);
    }

    /**
     * @param object|string $entity
     */
    public function class($entity): string
    {
        return $this->classMetaData($entity)->getName();
    }

    /**
     * @param object|string $entity
     */
    public function classMetaData($entity): ClassMetadata
    {
        $class = $this->entityToString($entity);

        assert(class_exists($class));

        return $this->manager($entity)->getClassMetadata($class);
    }

    /**
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

    /**
     * @param object|string $entity
     */
    private function entityToString($entity): string
    {
        return is_object($entity) ? get_class($entity) : $entity;
    }
}
