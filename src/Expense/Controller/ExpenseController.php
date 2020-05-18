<?php

declare(strict_types=1);

namespace App\Expense\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Expense\Entity\Expense;
use App\Expense\Event\ExpenseCreated;
use function assert;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExpenseController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new Expense($model->name);

        parent::persistEntity($entity);

        $this->event(new ExpenseCreated($entity));
    }
}
