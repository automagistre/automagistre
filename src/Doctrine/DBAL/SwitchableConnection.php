<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL;

use App\Entity\Embeddable\DatabaseConnectionOptions;
use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection as BaseConnection;
use ReflectionClass;
use ReflectionProperty;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SwitchableConnection extends BaseConnection
{
    /**
     * @var ReflectionProperty
     */
    private static $reflection;

    public function switch(DatabaseConnectionOptions $options, bool $connect = true): void
    {
        if ($this->isConnected()) {
            $this->close();
        }

        $this->setParams([
            'host' => $options->host,
            'dbname' => $options->name,
        ]);

        if ($connect) {
            $this->connect();
        }
    }

    private function setParams(array $params): void
    {
        $params = \array_merge($this->getParams(), $params);

        $property = $this->getProperty();
        $property->setAccessible(true);
        $property->setValue($this, $params);
        $property->setAccessible(false);
    }

    private function getProperty(): ReflectionProperty
    {
        if (null === self::$reflection) {
            $ref = $prev = new ReflectionClass($this);

            while ($ref instanceof ReflectionClass) {
                $prev = $ref;
                $ref = $ref->getParentClass();
            }

            self::$reflection = $prev->getProperty('params');
        }

        return self::$reflection;
    }
}
