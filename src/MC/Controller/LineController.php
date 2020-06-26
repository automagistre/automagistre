<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McLine;
use function assert;
use LogicException;
use Ramsey\Uuid\Uuid;
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
        $equipment = $this->getEntity(McEquipment::class);
        if (!$equipment instanceof McEquipment) {
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
    protected function persistEntity($entity): McLine
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new McLine(
            Uuid::uuid6(),
            $model->equipment,
            $model->work,
            $model->period,
            $model->recommended
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
