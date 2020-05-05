<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Doctrine\ORM\Type\Identifier;
use App\Manufacturer\Domain\Manufacturer;
use App\Manufacturer\Domain\ManufacturerId;
use App\Vehicle\Domain\Model;
use App\Vehicle\Domain\VehicleId;
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
     * @psalm-return T
     */
    public function findBy(string $class, array $criteria)
    {
        return $this->repository($class)->findOneBy($criteria);
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
        $class = [
            VehicleId::class => Model::class,
            CarId::class => Car::class,
            ManufacturerId::class => Manufacturer::class,
        ][get_class($identifier)];

        $view = $this->repository($class)
            ->createQueryBuilder('t')
            ->where('t.uuid = :id')
            ->setParameter('id', $identifier)
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        assert(is_array($view));

        return $view;
    }
}
