<?php

declare(strict_types=1);

namespace App\CreatedBy\EventListener;

use App\Costil;
use App\CreatedBy\Attributes\Exclude;
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
use function is_object;
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

        $classMetadata = $em->getClassMetadata(get_class($entity));

        if ($classMetadata->isIdentifierComposite) {
            return;
        }

        $idReflectionProperty = $classMetadata->getSingleIdReflectionProperty();

        foreach ($idReflectionProperty->getAttributes() as $attribute) {
            if (Exclude::class === $attribute->getName()) {
                return;
            }
        }

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
            $user instanceof User => $user->toId()->toString(),
            default => '00000000-0000-0000-0000-000000000000',
        };

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
            ],
        );
    }
}
