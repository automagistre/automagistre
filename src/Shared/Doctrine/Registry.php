<?php

declare(strict_types=1);

namespace App\Shared\Doctrine;

use App\Costil;
use App\Shared\Identifier\Identifier;
use function assert;
use function class_exists;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use function get_class;
use function is_array;
use function is_object;
use LogicException;
use function sprintf;
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
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return ?T
     */
    public function findBy(string $class, array $criteria)
    {
        return $this->repository($class)->findOneBy($criteria);
    }

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     *
     * @param Identifier|array $criteria
     */
    public function getBy(string $class, $criteria)
    {
        if ($criteria instanceof Identifier) {
            $criteria = [Costil::UUID_FIELDS[get_class($criteria)] => $criteria];
        }

        $entity = $this->repository($class)->findOneBy($criteria);

        assert($entity instanceof $class);

        return $entity;
    }

    /**
     * @psalm-param class-string $class
     */
    public function connection(string $class): Connection
    {
        return $this->manager($class)->getConnection();
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
        $entityRepository = $this->manager($class)->getRepository($class);

        assert($entityRepository instanceof EntityRepository);

        return $entityRepository;
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
     * @param mixed $entity
     *
     * @deprecated Must be removed with EventsListener
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

    public function view(Identifier $identifier): array
    {
        $class = Costil::ENTITY[get_class($identifier)];
        $uuidField = Costil::UUID_FIELDS[get_class($identifier)];

        $view = $this->repository($class)
            ->createQueryBuilder('t')
            ->where(sprintf('t.%s = :id', $uuidField))
            ->setParameter('id', $identifier)
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        assert(is_array($view));

        return $view;
    }

    /**
     * @psalm-param class-string $class
     */
    public function viewListBy(string $class, array $criteria): array
    {
        $qb = $this->manager($class)
            ->createQueryBuilder()
            ->select('t')
            ->from($class, 't');

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $qb
                    ->andWhere(sprintf('t.%s IN (:%s)', $field, $field))
                    ->setParameter($field, $value);
            } else {
                $qb
                    ->andWhere(sprintf('t.%s = :%s', $field, $field))
                    ->setParameter($field, $value);
            }
        }

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @template T
     *
     * @psalm-param class-string<T> $class
     *
     * @param mixed $id
     *
     * @psalm-return T
     */
    public function reference(string $class, $id)
    {
        return $this->manager($class)->getReference($class, $id);
    }
}
