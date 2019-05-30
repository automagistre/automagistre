<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Tenant\Expense;
use App\Entity\Tenant\ExpenseItem;
use App\Event\ExpenseItemCreated;
use App\Manager\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExpenseItemController extends AbstractController
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    protected function createNewEntity(): \stdClass
    {
        /** @var \stdClass $model */
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
    protected function persistEntity($entity): void
    {
        $model = $entity;
        \assert($model instanceof \stdClass);

        $entity = $this->em->transactional(function (EntityManagerInterface $em) use ($model): ExpenseItem {
            $entity = new ExpenseItem($model->expense, $model->amount, $this->getUser(), $model->description);
            $em->persist($entity);

            $expense = $entity->getExpense();

            $description = \sprintf('# Списание по статье расходов - "%s"', $expense->getName());
            if (null !== $entity->getDescription()) {
                $description .= \sprintf(', с комментарием "%s"', $entity->getDescription());
            }

            $this->paymentManager->createPayment($model->wallet, $description, $entity->getAmount()->negative());

            return $entity;
        });

        $this->event(new ExpenseItemCreated($entity));

        $this->setReferer($this->generateEasyPath($entity, 'list'));
    }
}
