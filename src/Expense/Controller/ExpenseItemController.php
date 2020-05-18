<?php

declare(strict_types=1);

namespace App\Expense\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Expense\Entity\Expense;
use App\Expense\Entity\ExpenseItem;
use App\Expense\Event\ExpenseItemCreated;
use App\Payment\Manager\PaymentManager;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use function sprintf;
use stdClass;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExpenseItemController extends AbstractController
{
    private PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    protected function createNewEntity(): stdClass
    {
        /** @var stdClass $model */
        $model = parent::createNewEntity();

        $expense = $this->getEntity(Expense::class);
        if ($expense instanceof Expense) {
            $model->expense = $expense;
            $model->wallet = $expense->getWallet();
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): ExpenseItem
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = $this->em->transactional(function (EntityManagerInterface $em) use ($model): ExpenseItem {
            $entity = new ExpenseItem($model->expense, $model->amount, $model->description);
            $em->persist($entity);

            $expense = $entity->getExpense();

            $description = sprintf('# Списание по статье расходов - "%s"', $expense->getName());
            if (null !== $entity->getDescription()) {
                $description .= sprintf(', с комментарием "%s"', $entity->getDescription());
            }

            $this->paymentManager->createPayment($model->wallet, $description, $entity->getAmount()->negative());

            return $entity;
        });

        $this->event(new ExpenseItemCreated($entity));

        $this->setReferer($this->generateEasyPath($entity, 'list'));

        return $entity;
    }
}
