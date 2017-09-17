<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TimestampableListener implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata => 'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $event->getClassMetadata();

        if ($classMetadata->hasField('createdAt')) {
            $classMetadata->addLifecycleCallback('updateCreatedAt', Events::prePersist);
        }

        if ($classMetadata->hasField('updatedAt')) {
            $classMetadata->addLifecycleCallback('updateUpdatedAt', Events::preUpdate);
        }
    }
}
