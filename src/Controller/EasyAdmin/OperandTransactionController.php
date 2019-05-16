<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Wallet;
use App\Form\Model\OperandTransaction;
use App\Manager\PaymentManager;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OperandTransactionController extends AbstractController
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    protected function createNewEntity(): OperandTransaction
    {
        $recipient = $this->getEntity(Operand::class);
        if (!$recipient instanceof Operand) {
            throw new LogicException('Operand required.');
        }

        $request = $this->request;
        if (!$request->query->has('type')) {
            throw new LogicException('Type required.');
        }

        $model = new OperandTransaction();
        $model->recipient = $recipient;
        $model->increment = 'increment' === $request->query->getAlnum('type');

        $registry = $this->container->get(Registry::class);

        $model->wallet = $registry->repository(Wallet::class)
            ->findOneBy(['defaultInManualTransaction' => true]);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForm($entity, array $entityProperties, $view): FormInterface
    {
        $form = parent::createEntityForm($entity, $entityProperties, $view);

        $form->add('wallet', EntityType::class, [
            'label' => 'Учитывать в',
            'class' => Wallet::class,
            'required' => false,
            'placeholder' => 'Не дублировать начисление',
        ]);

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        \assert($entity instanceof \stdClass);

        $this->em->transactional(function () use ($entity): void {
            /** @var Money $money */
            $money = $entity->amount;
            $money = $entity->increment ? $money->absolute() : $money->negative();

            $transaction = $this->paymentManager->createPayment($entity->recipient, $entity->description, $money);

            if ($entity->wallet instanceof Wallet) {
                $description = \sprintf(
                    '# Ручная транзакция "%s" для "%s", с комментарием "%s"',
                    $transaction->getId(),
                    (string) $entity->recipient,
                    $entity->description
                );

                $this->paymentManager->createPayment($entity->wallet, $description, $money);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $recipient = $this->getEntity(Operand::class);
        if ($recipient instanceof Operand) {
            $qb->andWhere('entity.recipient.id = :recipient')
                ->setParameter('recipient', $recipient->getId());
        }

        $qb->orderBy('entity.createdAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC');

        return $qb;
    }
}
