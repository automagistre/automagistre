<?php

declare(strict_types=1);

namespace App\Tenant\Doctrine;

use App\Tenant\Enum\Tenant;
use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use function assert;

final class ConnectionSwitcher
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * @psalm-suppress PossiblyInvalidFunctionCall
     * @psalm-suppress InaccessibleProperty
     */
    public function switch(?Tenant $tenant): void
    {
        $connection = $this->registry->getConnection();
        assert($connection instanceof Connection);

        if ($connection->isConnected()) {
            $connection->close();
        }

        (Closure::bind(static function (Connection $conn) use ($tenant): void {
            $newOne = $tenant?->toDatabase();

            $conn->params['host'] = $newOne['host'] ?? 'undefined';
        }, null, $connection))($connection);
    }
}
