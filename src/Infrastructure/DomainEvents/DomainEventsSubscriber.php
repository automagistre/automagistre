<?php

declare(strict_types=1);

namespace App\Infrastructure\DomainEvents;

use function array_shift;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sentry\SentryBundle\SentryBundle;
use function spl_object_hash;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

final class DomainEventsSubscriber implements EventSubscriber
{
    /**
     * @var array<string, Event>
     */
    private array $events = [];

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->doCollect($event);
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $this->doCollect($event);
    }

    /**
     * You need to listen to preRemove if you use soft delete from Doctrine extensions,
     * because it prevents postRemove from being called.
     */
    public function preRemove(LifecycleEventArgs $event): void
    {
        $this->doCollect($event);
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $this->doCollect($event);
    }

    public function postFlush(): void
    {
        $this->dispatchCollectedEvents();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function dispatchCollectedEvents(): void
    {
        while ($event = array_shift($this->events)) {
            try {
                $this->eventDispatcher->dispatch($event);
            } catch (Throwable $e) {
                SentryBundle::getCurrentHub()->captureException($e);
            }
        }
    }

    private function doCollect(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        if (!$entity instanceof RaiseEventsInterface) {
            return;
        }

        foreach ($entity->popEvents() as $domainEvent) {
            // We index by object hash, not to have the same event twice
            $this->events[spl_object_hash($domainEvent)] = $domainEvent;
        }
    }
}
