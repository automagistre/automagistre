<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Costil;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use function assert;
use function get_class;
use function is_int;
use function is_object;
use function method_exists;
use const PHP_SAPI;

final class PostPersistEventListener implements EventSubscriber
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
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $em = $args->getObjectManager();
        assert($em instanceof EntityManagerInterface);
        $entity = $args->getObject();

        $classMetadata = $em->getClassMetadata(get_class($entity));

        if ($classMetadata->isIdentifierComposite) {
            return;
        }

        $idReflectionProperty = $classMetadata->getSingleIdReflectionProperty();

        $id = $idReflectionProperty->getValue($entity);

        if (is_object($id) && method_exists($id, 'toString')) {
            $id = $id->toString();
        }

        if (is_int($id) || null === $id) {
            return;
        }

        $user = $this->security->getUser();
        $userId = match (true) {
            null === $user && 'cli' === PHP_SAPI => Costil::SERVICE_USER,
            null !== $user => $user->getUserIdentifier(),
            default => Costil::ANONYMOUS,
        };

        $em->getConnection()->executeQuery(
            'INSERT INTO created_by (id, user_id, created_at) VALUES (:id, :user, :date)',
            [
                'id' => (string) $id,
                'user' => $userId,
                'date' => new DateTimeImmutable(),
            ],
            [
                'user' => 'user_id',
                'date' => 'datetime',
            ],
        );
    }
}
