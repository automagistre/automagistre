<?php

namespace App\MessageBus;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;

class EntityRecordedMessageCollectorListener implements EventSubscriber, ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function preFlush(PreFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getIdentityMap() as $entities) {
            foreach ($entities as $entity) {
                $this->collectEventsFromEntity($entity);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->collectEventsFromEntity($entity);
        }
    }

    /**
     * We need to listen on postFlush for Lifecycle Events
     * All Lifecycle callback events are triggered after the onFlush event.
     */
    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getIdentityMap() as $entities) {
            foreach ($entities as $entity) {
                $this->collectEventsFromEntity($entity);
            }
        }
    }

    private function collectEventsFromEntity(object $entity): void
    {
        if (!$entity instanceof ContainsRecordedMessages) {
            return;
        }

        foreach ($entity->eraseMessages() as $event) {
            $this->record($event);
        }
    }
}
