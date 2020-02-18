<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Landlord\MC\Work;
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
    protected function persistEntity($entity): Work
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new Work($model->name, $model->description, $model->price);

        parent::persistEntity($entity);

        return $entity;
    }
}
