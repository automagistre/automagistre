<?php

declare(strict_types=1);

namespace App\Tenant;

use App\Shared\Doctrine\Registry;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityChecker
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function isTenantEntity(string $entity): bool
    {
        $manager = $this->registry->managerOrNull($entity);
        if (null === $manager) {
            return false;
        }

        return 'tenant' === $manager->getConnection()->getDatabase();
    }
}
