<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Landlord\MC\Equipment;
use App\Entity\Landlord\MC\Line;
use function assert;
use LogicException;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LineController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): stdClass
    {
        $model = new stdClass();
        $equipment = $this->getEntity(Equipment::class);
        if (!$equipment instanceof Equipment) {
            throw new LogicException('Equipment required.');
        }

        $model->id = null;
        $model->equipment = $equipment;
        $model->work = null;
        $model->period = null;
        $model->recommended = null;

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Line
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new Line($model->equipment, $model->work, $model->period, $model->recommended);

        parent::persistEntity($entity);

        return $entity;
    }
}
