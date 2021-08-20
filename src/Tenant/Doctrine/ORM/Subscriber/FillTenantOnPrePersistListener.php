<?php

declare(strict_types=1);

namespace App\Tenant\Doctrine\ORM\Subscriber;

use App\Tenant\Entity\TenantEntity;
use App\Tenant\State;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

final class FillTenantOnPrePersistListener implements EventSubscriberInterface
{
    public function __construct(private State $state)
    {
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

    /**
     * @psalm-suppress InaccessibleProperty
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        if (!$entity instanceof TenantEntity) {
            return;
        }

        $entity->tenantId = $this->state->get();
    }
}
