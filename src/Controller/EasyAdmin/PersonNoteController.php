<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\OperandNote;
use App\Entity\Person;
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

        return new OperandNote($order, $this->getUser());
    }
}
