<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\OperandNote;
use App\Customer\Entity\Organization;
use App\EasyAdmin\Controller\AbstractController;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrganizationNoteController extends AbstractController
{
    protected function createNewEntity(): OperandNote
    {
        $order = $this->getEntity(Organization::class);
        if (!$order instanceof Organization) {
            throw new LogicException('Organization required.');
        }

        return new OperandNote($order, $this->getUser()->toId());
    }
}
