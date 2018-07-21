<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Income;
use App\Entity\Motion;
use App\Form\Model\Income as IncomeModel;
use App\Form\Model\IncomePart as IncomePartModel;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): IncomeModel
    {
        return new IncomeModel();
    }

    /**
     * @param IncomeModel $model
     */
    protected function persistEntity($model): void
    {
        $this->em->transactional(function (EntityManagerInterface $em) use ($model): void {
            $parts = array_map(function (IncomePartModel $model) {
                return [$model->part, $model->price, $model->quantity];
            }, $model->parts);

            $income = new Income($model->supplier, $parts, $this->getUser());

            parent::persistEntity($income);

            foreach ($income->getIncomeParts() as $item) {
                $em->persist(new Motion($item->getPart(), $item->getQuantity()));
            }

            $em->flush();
        });
    }
}
