<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Organization;
use App\Events;
use Symfony\Component\EventDispatcher\GenericEvent;

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

        $this->event(Events::ORGANIZATION_CREATED, new GenericEvent($entity));
    }
}
