<?php

declare(strict_types=1);

namespace App\Tenant\Doctrine\ORM\Query;

use App\Tenant\Entity\TenantGroupEntity;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

final class GroupFilter extends SQLFilter
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->reflClass->isSubclassOf(TenantGroupEntity::class)) {
            return '';
        }

        return sprintf('%s.tenant_group_id = %s', $targetTableAlias, $this->getParameter('tenant_group_id'));
    }
}
