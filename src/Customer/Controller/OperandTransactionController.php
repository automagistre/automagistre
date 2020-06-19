<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\Operand;
use App\Customer\Form\OperandTransactionModel;
use App\EasyAdmin\Controller\AbstractController;
use App\Entity\Tenant\OperandTransaction;
use App\Payment\Manager\PaymentManager;
use App\Wallet\Entity\Wallet;
use function assert;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use function sprintf;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OperandTransactionController extends AbstractController
{
    private PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    protected function createNewEntity(): OperandTransactionModel
    {
        $recipient = $this->getEntity(Operand::class);
        if (!$recipient instanceof Operand) {
            throw new LogicException('Operand required.');
        }

        $request = $this->request;
        if (!$request->query->has('type')) {
            throw new LogicException('Type required.');
        }

        $model = new OperandTransactionModel();
        $model->recipient = $recipient;
        $model->increment = 'increment' === $request->query->getAlnum('type');

        $model->wallet = $this->registry->repository(Wallet::class)
            ->findOneBy(['defaultInManualTransaction' => true]);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForm($entity, array $entityProperties, $view): FormInterface
    {
        $form = parent::createEntityForm($entity, $entityProperties, $view);

        $form->add('wallet', ChoiceType::class, [
            'label' => 'Учитывать в',
            'choice_loader' => new CallbackChoiceLoader(function (): array {
                return $this->registry->repository(Wallet::class)->findAll();
            }),
            'choice_label' => fn (Wallet $wallet) => $wallet->name,
            'choice_value' => fn (?Wallet $wallet) => null === $wallet ? null : $wallet->toId()->toString(),
            'required' => false,
            'placeholder' => 'Не дублировать начисление',
        ]);

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): OperandTransaction
    {
        $model = $entity;
        assert($model instanceof OperandTransactionModel);

        return $this->em->transactional(function () use ($model): OperandTransaction {
            /** @var Money $money */
            $money = $model->amount;
            $money = $model->increment ? $money->absolute() : $money->negative();

            $transaction = $this->paymentManager->createPayment($model->recipient, $model->description, $money);
            assert($transaction instanceof OperandTransaction);

            if ($model->wallet instanceof Wallet) {
                $description = sprintf(
                    '# Ручная транзакция "%s" для "%s", с комментарием "%s"',
                    $transaction->getId(),
                    (string) $model->recipient,
                    $model->description
                );

                $this->paymentManager->createPayment($model->wallet, $description, $money);
            }

            return $transaction;
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
