<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\CarNote;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarNoteController extends AbstractController
{
    protected function createNewEntity(): CarNote
    {
        $order = $this->getEntity(Car::class);
        if (!$order instanceof Car) {
            throw new LogicException('Car required.');
        }

        return new CarNote($order, $this->getUser());
    }
}
