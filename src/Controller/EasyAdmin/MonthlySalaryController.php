<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Tenant\Employee;
use App\Entity\Tenant\MonthlySalary;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MonthlySalaryController extends AbstractController
{
    protected function createNewEntity(): \stdClass
    {
        $employee = $this->getEntity(Employee::class);
        if (!$employee instanceof Employee) {
            throw new LogicException('Employee required.');
        }

        $model = new \stdClass();
        $model->employee = $employee;
        $model->id
            = $model->payday
            = $model->amount
            = null;

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $model = $entity;
        \assert($model instanceof \stdClass);

        $entity = new MonthlySalary($model->employee, $model->payday, $model->amount, $this->getUser());

        parent::persistEntity($entity);
    }
}
