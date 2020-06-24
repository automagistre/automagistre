<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Costil;
use App\User\Entity\User;
use function assert;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use function get_class;
use function is_int;
use function method_exists;
use const PHP_SAPI;
use Symfony\Component\Security\Core\Security;

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
