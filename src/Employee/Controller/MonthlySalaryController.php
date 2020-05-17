<?php

declare(strict_types=1);

namespace App\Employee\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Employee\Entity\Employee;
use App\Employee\Entity\MonthlySalary;
use function assert;
use LogicException;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MonthlySalaryController extends AbstractController
{
    protected function createNewEntity(): stdClass
    {
        $employee = $this->getEntity(Employee::class);
        if (!$employee instanceof Employee) {
            throw new LogicException('Employee required.');
        }

        $model = new stdClass();
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
    protected function persistEntity($entity): MonthlySalary
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new MonthlySalary($model->employee, $model->payday, $model->amount, $this->getUser());

        parent::persistEntity($entity);

        return $entity;
    }
}
