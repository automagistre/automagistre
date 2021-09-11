<?php

declare(strict_types=1);

namespace App\Tenant\EventListener;

use App\Tenant\Event\TenantChanged;
use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function assert;

final class SwitchConnectionOnTenantChanged implements EventSubscriberInterface
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TenantChanged::class => 'onTenantChanged',
        ];
    }

    /**
     * @psalm-suppress PossiblyInvalidFunctionCall
     * @psalm-suppress InaccessibleProperty
     */
    public function onTenantChanged(TenantChanged $event): void
    {
        $connection = $this->registry->getConnection();
        assert($connection instanceof Connection);

        if ($connection->isConnected()) {
            $connection->close();
        }

        $tenant = $event->tenant;

        (Closure::bind(static function (Connection $conn) use ($tenant): void {
            $newOne = $tenant?->toDatabase();

            $conn->params['host'] = $newOne['host'] ?? 'postgres';
        }, null, $connection))($connection);
    }
}
