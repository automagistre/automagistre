<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Income;
use App\Entity\Motion;
use App\Form\Model\Income as IncomeModel;
use App\Form\Model\IncomePart as IncomePartModel;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeControllerEasy extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity()
    {
        return new IncomeModel();
    }

    /**
     * @param IncomeModel $model
     *
     * @throws \Exception
     */
    protected function persistEntity($model): void
    {
        $parts = array_map(function (IncomePartModel $model) {
            return [$model->part, $model->price, $model->quantity];
        }, $model->parts);

        $this->em->beginTransaction();

        try {
            $income = new Income($model->supplier, $parts, $this->getUser());
            parent::persistEntity($income);

            foreach ($income->getIncomeParts() as $item) {
                $this->em->persist(new Motion($item->getPart(), $item->getQuantity()));
            }
            $this->em->flush();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }

        $this->em->commit();
    }
}
