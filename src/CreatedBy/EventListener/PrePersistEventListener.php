<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Keycloak\Entity\UserId;
use App\Costil;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use const PHP_SAPI;

final class PrePersistEventListener implements EventSubscriber
{
    public function __construct(private Security $security)
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

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (property_exists($entity, 'createdBy')) {
            $user = $this->security->getUser();
            $entity->createdBy = UserId::from(match (true) {
                null === $user && 'cli' === PHP_SAPI => Costil::SERVICE_USER,
                null !== $user => $user->getUserIdentifier(),
                default => Costil::ANONYMOUS,
            });
        }

        if (property_exists($entity, 'createdAt')) {
            $entity->createdAt = new DateTimeImmutable();
        }
    }
}
