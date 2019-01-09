<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Landlord\MC\Equipment;
use App\Entity\Landlord\MC\Line;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LineController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Line
    {
        $entity = new Line();
        $equipment = $this->getEntity(Equipment::class);
        if (!$equipment instanceof Equipment) {
            throw new LogicException('Equipment required.');
        }

        $entity->equipment = $equipment;

        return $entity;
    }
}
