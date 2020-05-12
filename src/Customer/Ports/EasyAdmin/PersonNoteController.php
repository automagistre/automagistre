<?php

declare(strict_types=1);

namespace App\Customer\Ports\EasyAdmin;

use App\Controller\EasyAdmin\AbstractController;
use App\Customer\Domain\OperandNote;
use App\Customer\Domain\Person;
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
