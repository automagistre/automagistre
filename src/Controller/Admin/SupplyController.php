<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Part;
use App\Entity\Supply;
use App\Form\Model\Supply as SupplyModel;
use Money\Currency;
use Money\Money;

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
        $money = new Money($model->price, new Currency('RUB'));

        parent::persistEntity(
            new Supply($model->supplier, $model->part, $money, $model->quantity)
        );
    }
}
