<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\CreatedBy\Entity\CreatedBy;
use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\User\Entity\User;
use function assert;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use function is_int;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Security;

final class PrePersistEventListener implements EventSubscriber
{
    private Registry $registry;

    private Security $security;

    public function __construct(Registry $registry, Security $security)
    {
        $this->registry = $registry;
        $this->security = $security;
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
        if ($entity instanceof CreatedBy) {
            return;
        }

        $id = $this->registry->classMetaData($entity)->getSingleIdReflectionProperty()->getValue($entity);

        if (is_int($id) || null === $id) {
            return;
        }

        if ($id instanceof Identifier) {
            $id = $id->toUuid();
        }

        assert($id instanceof UuidInterface);

        $user = $this->security->getUser();

        $userId = null;
        if ($user instanceof User) {
            $userId = $user->toId();
        }

        $em = $args->getObjectManager();
        $em->persist(new CreatedBy($id, $userId));
    }
}
