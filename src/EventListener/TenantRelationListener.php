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

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TenantRelationListener implements EventSubscriber
{
    /**
     * @var array
     */
    private static $map = [];

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
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

        foreach ($classMetadata->embeddedClasses as $property => $embedded) {
            $embeddedClass = $embedded['class'];

            if (\is_subclass_of($embeddedClass, Relation::class, true)) {
                self::$map[$classMetadata->getName()][$property] = $embeddedClass;
            }
        }
    }

    public function postLoad(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        \assert(\method_exists($entity, 'getId'));

        $classMetadata = $this->registry->classMetaData($entity);

        $entityClass = $classMetadata->getName();

        if (!\array_key_exists($entityClass, self::$map)) {
            return;
        }

        foreach (self::$map[$entityClass] as $property => $class) {
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
