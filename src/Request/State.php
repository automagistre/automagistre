<?php

declare(strict_types=1);

namespace App\Request;

use App\Doctrine\DBAL\SwitchableConnection;
use App\Entity\Landlord\Tenant;
use InvalidArgumentException;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class State
{
    /**
     * @var Tenant
     */
    private $tenant;

    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function tenant(string $tenant = null): Tenant
    {
        if (null !== $tenant) {
            $entity = $this->registry->getEntityManagerForClass(Tenant::class)
                ->getRepository(Tenant::class)
                ->findOneBy(['identifier' => $tenant]);

            if (!$entity instanceof Tenant) {
                throw new InvalidArgumentException(\sprintf('Tenant "%s" not exist.', $tenant));
            }

            $this->switch($tenant);
            $this->tenant = $entity;
        }

        return $this->tenant;
    }

    private function switch(string $tenant): void
    {
        $connection = $this->registry->getConnection('tenant');
        if (!$connection instanceof SwitchableConnection) {
            throw new LogicException('SwitchableConnection required');
        }

        $connection->switch($tenant);
    }
}
