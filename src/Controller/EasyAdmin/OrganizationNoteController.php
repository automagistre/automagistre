<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\OperandNote;
use App\Entity\Landlord\Organization;
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

        return new OperandNote($order, $this->getUser());
    }
}
