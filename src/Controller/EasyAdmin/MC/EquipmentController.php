<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Entity\MC\Equipment;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EquipmentController extends AbstractController
{
    /**
     * @param Equipment $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath($entity, 'show'));
    }
}
