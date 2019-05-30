<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Organization;
use App\Event\OrganizationCreated;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrganizationController extends OperandController
{
    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        \assert($entity instanceof Organization);

        parent::persistEntity($entity);

        $this->event(new OrganizationCreated($entity));
    }
}
