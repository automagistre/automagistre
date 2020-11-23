<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\MC\Entity\McWork;
use App\MC\Entity\McWorkId;
use function assert;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WorkController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): stdClass
    {
        $model = new stdClass();

        $model->id = null;
        $model->name = null;
        $model->description = null;
        $model->price = null;

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): McWork
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new McWork(
            McWorkId::generate(),
            $model->name,
            $model->description,
            $model->price
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
