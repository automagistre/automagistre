<?php

declare(strict_types=1);

namespace App\Tenant;

use function array_merge;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use LogicException;
use ReflectionClass;
use ReflectionProperty;
use function sprintf;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ConnectionSwitcher
{
    private ManagerRegistry $registry;

    private ?Connection $connection;

    private ?ReflectionProperty $reflection;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function switch(Tenant $tenant, bool $connect = false): void
    {
        $connection = $this->getConnection();

        if ($connection->isConnected()) {
            $connection->close();
        }

        $this->setParams($connection, [
            'host' => sprintf('tenant_%s', $tenant->getIdentifier()),
        ]);

        if ($connect) {
            $connection->connect();
        }
    }

    private function setParams(Connection $connection, array $params): void
    {
        $params = array_merge($connection->getParams(), $params);

        $property = $this->getProperty($connection);
        $property->setAccessible(true);
        $property->setValue($connection, $params);
        $property->setAccessible(false);
    }

    private function getConnection(): Connection
    {
        if (null === $this->connection) {
            $connection = $this->registry->getConnection('tenant');
            if (!$connection instanceof Connection) {
                throw new LogicException('Connection required');
            }

            $this->connection = $connection;
        }

        return $this->connection;
    }

    private function getProperty(Connection $connection): ReflectionProperty
    {
        if (null === $this->reflection) {
            $ref = $prev = new ReflectionClass($connection);

            while ($ref instanceof ReflectionClass) {
                $prev = $ref;
                $ref = $ref->getParentClass();
            }

            $this->reflection = $prev->getProperty('params');
        }

        return $this->reflection;
    }
}
