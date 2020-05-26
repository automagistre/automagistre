<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\OperandNote;
use App\Customer\Entity\Person;
use App\EasyAdmin\Controller\AbstractController;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonNoteController extends AbstractController
{
    protected function createNewEntity(): OperandNote
    {
        $order = $this->getEntity(Person::class);
        if (!$order instanceof Person) {
            throw new LogicException('Person required.');
        }

        return new OperandNote($order, $this->getUser()->toId());
    }
}
