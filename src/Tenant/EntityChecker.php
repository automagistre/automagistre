<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Doctrine\Registry;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityChecker
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param object|string $entity
     */
    public function isTenantEntity($entity): bool
    {
        return $this->registry->isEntity($entity)
            && 'tenant' === $this->registry->manager($entity)->getConnection()->getDatabase();
    }
}
