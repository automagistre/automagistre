<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Registry;
use App\Entity\Embeddable\Relation;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use LogicException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantRelationListener implements EventSubscriber
{
    private const CACHE_KEY = 'tenant_mapping';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(Registry $registry, CacheItemPoolInterface $cache)
    {
        $this->registry = $registry;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $classMetadata = $event->getClassMetadata();

        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        $map = $cacheItem->get() ?? [];
        foreach ($classMetadata->embeddedClasses as $property => $embedded) {
            $embeddedClass = $embedded['class'];

            if (\is_subclass_of($embeddedClass, Relation::class, true)) {
                $map[$classMetadata->getName()][$property] = $embeddedClass;
            }
        }

        $cacheItem->set($map);
        $this->cache->save($cacheItem);
    }

    public function postLoad(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        \assert(\method_exists($entity, 'getId'));

        $classMetadata = $this->registry->classMetaData($entity);

        $entityClass = $classMetadata->getName();

        $map = $this->cache->getItem(self::CACHE_KEY)->get() ?? [];
        if (!\array_key_exists($entityClass, $map)) {
            return;
        }

        foreach ($map[$entityClass] as $property => $class) {
            $reflectionProperty = $classMetadata->getReflectionProperty($property);
            $reflectionProperty->setAccessible(true);

            $relation = $reflectionProperty->getValue($entity);

            if (!$relation instanceof Relation) {
                continue;
            }

            if ($relation->isEmpty()) {
                continue;
            }

            $relationEntityClass = $relation::entityClass();
            \assert(\class_exists($relationEntityClass));
            $relationEntity = $this->registry->manager($relationEntityClass)
                ->getRepository($relationEntityClass)
                ->find($relation->id());

            if (null === $relationEntity) {
                throw new LogicException(
                    \sprintf(
                        'Relation "%s" in field "%s" of entity "%s" are disappeared!',
                        $relationEntityClass,
                        $property,
                        $entityClass.'#'.$entity->getId()
                    )
                );
            }

            $reflectionProperty->setValue($entity, new $class($relationEntity));
        }
    }
}
