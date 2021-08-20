<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Costil;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Premier\Identifier\Identifier;
use Ramsey\Uuid\UuidInterface;
use function array_map;
use function assert;
use function class_exists;
use function is_array;
use function serialize;
use function sprintf;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Registry
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return array<int, T>
     */
    public function findBy(
        string $class,
        mixed $criteria = [],
        array $orderBy = null,
        int $limit = null,
        int $offset = null,
    ): array {
        if (!is_array($criteria)) {
            $criteria = ['id' => $criteria];
        }

        return $this->repository($class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return ?T
     */
    public function find(
        string $class,
        string | UuidInterface | Identifier $id,
        int $lockMode = null,
        int $lockVersion = null,
    ): mixed {
        return $this->repository($class)->find($id, $lockMode, $lockVersion);
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return ?T
     */
    public function findOneBy(string $class, array $criteria, array $ordering = [])
    {
        return $this->repository($class)->findOneBy($criteria, $ordering);
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     *
     * @param mixed      $id
     * @param null|mixed $lockMode
     * @param null|mixed $lockVersion
     */
    public function get(string $class, $id, $lockMode = null, $lockVersion = null)
    {
        $entity = $this->repository($class)->find($id, $lockMode, $lockVersion);

        if (!$entity instanceof $class) {
            throw new LogicException(sprintf(
                'Entity %s not found for criteria %s',
                $class,
                serialize($id),
            ));
        }

        return $entity;
    }

    /**
     * @template T of object
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     *
     * @param array|Identifier $criteria
     */
    public function getBy(string $class, $criteria)
    {
        if ($criteria instanceof Identifier) {
            $criteria = ['id' => $criteria];
        }

        $entity = $this->repository($class)->findOneBy($criteria);

        if (!$entity instanceof $class) {
            throw new LogicException(sprintf(
                'Entity %s not found for criteria %s',
                $class,
                serialize($criteria),
            ));
        }

        return $entity;
    }

    /**
     * @psalm-param class-string $class
     */
    public function connection(string $class = null): Connection
    {
        return $this->manager($class)->getConnection();
    }

    /**
     * @psalm-param class-string $class
     */
    public function manager(string $class = null): EntityManagerInterface
    {
        $em = $this->registry->getManager();

        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManager expected');
        }

        return $em;
    }

    /**
     * @template T of object
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
     * @psalm-param class-string $class
     */
    public function viewListBy(string $class, array $criteria, array $ordering = []): array
    {
        $qb = $this->manager($class)
            ->createQueryBuilder()
            ->select('t')
            ->from($class, 't')
        ;

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $qb
                    ->andWhere(sprintf('t.%s IN (:%s)', $field, $field))
                    ->setParameter($field, $value)
                ;
            } else {
                $qb
                    ->andWhere(sprintf('t.%s = :%s', $field, $field))
                    ->setParameter($field, $value)
                ;
            }
        }

        foreach ($ordering as $field => $direction) {
            $qb->addOrderBy('t.'.$field, $direction);
        }

        return array_map(fn (array $item) => Costil::convertToMoney($item), $qb->getQuery()->getArrayResult());
    }

    public function add(object ...$entities): self
    {
        $em = $this->manager();

        foreach ($entities as $entity) {
            $em->persist($entity);
        }

        return $this;
    }

    public function flush(string $manager = null): void
    {
        $managers = null === $manager
            ? $this->registry->getManagers()
            : [
                class_exists($manager)
                    ? $this->registry->getManagerForClass($manager)
                    : $this->registry->getManager($manager),
            ];

        foreach ($managers as $em) {
            assert($em instanceof EntityManagerInterface);

            $em->flush();
        }
    }
}
