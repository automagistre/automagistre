<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\User\Entity\User;
use function assert;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use function is_int;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Security;

final class PostPersistEventListener implements EventSubscriber
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
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
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
        assert($em instanceof EntityManagerInterface);

        $em->getConnection()->executeQuery(
            'INSERT INTO created_by (id, user_id, created_at) VALUES (:id, :user, :date)',
            [
                'id' => $id,
                'user' => $userId,
                'date' => new DateTimeImmutable(),
            ],
            [
                'id' => 'uuid',
                'user' => 'user_id',
                'date' => 'datetime',
            ]
        );
    }
}
