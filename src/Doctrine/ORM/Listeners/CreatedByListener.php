<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Listeners;

use App\Entity\Embeddable\UserRelation;
use App\State;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use function get_class;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CreatedByListener implements EventSubscriber
{
    /** @var State */
    private State $state;

    public function __construct(State $state)
    {
        $this->state = $state;
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

    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $classMetadata = $event->getEntityManager()->getClassMetadata(get_class($entity));

        if ($classMetadata->hasField('createdByRelation')) {
            $classMetadata->setFieldValue($entity, 'createdByRelation', new UserRelation($this->state->user()));
        }
    }
}
