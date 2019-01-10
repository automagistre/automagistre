<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Tenant\Expense;
use App\Events;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExpenseController extends AbstractController
{
    /**
     * @param \stdClass $model
     */
    protected function persistEntity($model): void
    {
        $entity = new Expense($model->name);

        parent::persistEntity($entity);

        $this->event(Events::EXPENSE_CREATED, $entity);
    }
}
