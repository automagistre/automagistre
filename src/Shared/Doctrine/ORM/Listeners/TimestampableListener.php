<?php

declare(strict_types=1);

namespace App\Shared\Doctrine\ORM\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TimestampableListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata => 'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        $classMetadata = $event->getClassMetadata();

        $reflectionClass = $classMetadata->getReflectionClass();
        if ($classMetadata->hasField('createdAt') && $reflectionClass->hasMethod('updateCreatedAt')) {
            $classMetadata->addLifecycleCallback('updateCreatedAt', Events::prePersist);
        }

        if ($classMetadata->hasField('updatedAt') && $reflectionClass->hasMethod('updateUpdatedAt')) {
            $classMetadata->addLifecycleCallback('updateUpdatedAt', Events::preUpdate);
        }
    }
}
