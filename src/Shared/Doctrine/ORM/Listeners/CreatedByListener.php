<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Listeners;

use App\State;
use App\User\Entity\User;
use function assert;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use function get_class;
use function method_exists;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CreatedByListener implements EventSubscriber
{
    /** @var State */
    private State $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $classMetadata = $event->getEntityManager()->getClassMetadata(get_class($entity));

        $reflectionClass = $classMetadata->getReflectionClass();

        while (false !== $reflectionClass && !$reflectionClass->hasProperty('createdBy')) {
            $reflectionClass = $reflectionClass->getParentClass();
        }

        if (false === $reflectionClass) {
            return;
        }

        $reflectionProperty = $reflectionClass->getProperty('createdBy');
        $reflectionType = $reflectionProperty->getType();
        assert(null !== $reflectionType);
        assert(method_exists($reflectionType, 'getName'));
        if (User::class !== $reflectionType->getName()) {
            return;
        }

        $classMetadata->setFieldValue($entity, 'createdBy', $this->state->user());
    }
}
