<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Costil;
use App\User\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use function assert;
use function get_class;
use function is_int;
use function method_exists;
use const PHP_SAPI;

final class PostPersistEventListener implements EventSubscriber
{
    private Security $security;

    public function __construct(Security $security)
    {
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
        $em = $args->getObjectManager();
        assert($em instanceof EntityManagerInterface);
        $entity = $args->getObject();

        if (method_exists($entity, 'toId')) {
            $id = $entity->toId();
        } else {
            $id = $em->getClassMetadata(get_class($entity))->getSingleIdReflectionProperty()->getValue($entity);
        }

        if (is_int($id) || null === $id) {
            return;
        }

        $user = $this->security->getUser();

        $userId = null;

        if ($user instanceof User) {
            $userId = $user->toId();
        }

        if (null === $userId && 'cli' === PHP_SAPI) {
            $userId = Costil::SERVICE_USER;
        }

        if (null === $userId) {
            $em->getConnection()->executeQuery(
                'INSERT INTO created_at (id, created_at) VALUES (:id, :date)',
                [
                    'id' => (string) $id,
                    'date' => new DateTimeImmutable(),
                ],
                [
                    'id' => 'uuid',
                    'date' => 'datetime',
                ]
            );

            return;
        }

        $em->getConnection()->executeQuery(
            'INSERT INTO created_by (id, user_id, created_at) VALUES (:id, :user, :date)',
            [
                'id' => (string) $id,
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
