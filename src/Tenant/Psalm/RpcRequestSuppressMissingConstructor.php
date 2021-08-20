<?php

declare(strict_types=1);

namespace App\Tenant\Psalm;

use App\Tenant\Entity\TenantEntity;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;
use ReflectionClass;

final class RpcRequestSuppressMissingConstructor implements AfterClassLikeVisitInterface
{
    /**
     * {@inheritdoc}
     */
    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $storage = $event->getStorage();

        if ($storage->user_defined
            && !$storage->is_interface
            && class_exists($storage->name)
            && (new ReflectionClass($storage->name))->isSubclassOf(TenantEntity::class)
        ) {
            $storage->suppressed_issues[-1] = 'PropertyNotSetInConstructor';
        }
    }
}
