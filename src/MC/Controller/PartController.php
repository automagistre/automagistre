<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\MC\Entity\McLine;
use App\MC\Entity\McPart;
use function assert;
use LogicException;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): stdClass
    {
        $model = new stdClass();

        $line = $this->getEntity(McLine::class);
        if (!$line instanceof McLine) {
            throw new LogicException('Line required.');
        }

        $model->id = null;
        $model->line = $line;
        $model->part = null;
        $model->quantity = null;
        $model->recommended = null;

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): McPart
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new McPart($model->line, $model->part, $model->quantity, $model->recommended);

        parent::persistEntity($entity);

        return $entity;
    }
}
