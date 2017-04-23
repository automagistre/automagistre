<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Part;
use App\Entity\Supply;
use App\Form\Model\Supply as SupplyModel;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplyController extends AdminController
{
    protected function createNewEntity()
    {
        $requestQuery = $this->request->query;

        $parameters = [];
        if ($part = $requestQuery->get('part')) {
            $parameters['part'] = $this->em->getRepository(Part::class)->find($part);
        }

        if ($quantity = $requestQuery->getInt('quantity')) {
            $parameters['quantity'] = $quantity;
        }

        return new SupplyModel($parameters);
    }

    /**
     * @param SupplyModel $model
     */
    protected function persistEntity($model): void
    {
        parent::persistEntity(
            new Supply($model->supplier, $model->part, $model->price, $model->quantity)
        );
    }
}
