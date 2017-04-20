<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Supply;
use App\Model\Supply as SupplyModel;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplyController extends AdminController
{
    protected function createNewEntity()
    {
        return new SupplyModel();
    }

    /**
     * @param SupplyModel $model
     */
    protected function persistEntity($model): void
    {
        parent::persistEntity(new Supply($model->supplier, $model->part, $model->quantity, $this->getUser()));
    }
}
