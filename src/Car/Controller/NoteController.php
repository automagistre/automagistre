<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\Note;
use App\EasyAdmin\Controller\AbstractController;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NoteController extends AbstractController
{
    protected function createNewEntity(): Note
    {
        $order = $this->getEntity(Car::class);
        if (!$order instanceof Car) {
            throw new LogicException('Car required.');
        }

        return new Note($order, $this->getUser()->toId());
    }
}
