<?php

declare(strict_types=1);

namespace App\Customer\Ports\EasyAdmin;

use App\Customer\Domain\OperandId;
use App\Customer\Domain\Organization;
use App\Customer\Event\OrganizationCreated;
use function assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrganizationController extends OperandController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Organization
    {
        return new Organization(OperandId::generate());
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Organization);

        parent::persistEntity($entity);

        $this->event(new OrganizationCreated($entity));
    }
}
