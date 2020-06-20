<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\Operand;
use App\Customer\Enum\CustomerTransactionSource;
use App\Customer\Form\TransactionDto;
use App\EasyAdmin\Controller\AbstractController;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class TransactionController extends AbstractController
{
    protected function createNewEntity(): TransactionDto
    {
        $recipient = $this->getEntity(Operand::class);
        if (!$recipient instanceof Operand) {
            throw new LogicException('Operand required.');
        }

        $request = $this->request;
        if (!$request->query->has('type')) {
            throw new LogicException('Type required.');
        }

        $model = $this->createWithoutConstructor(TransactionDto::class);
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
    protected function persistEntity($entity): CustomerTransaction
    {
        $model = $entity;
        assert($model instanceof TransactionDto);

        return $this->em->transactional(function (EntityManagerInterface $em) use ($model): CustomerTransaction {
            /** @var Money $money */
            $money = $model->amount;
            $money = $model->increment ? $money->absolute() : $money->negative();

            $customerTransactionId = CustomerTransactionId::generate();
            $transaction = new CustomerTransaction(
                $customerTransactionId,
                $model->recipient->toId(),
                $money,
                CustomerTransactionSource::manual(),
                $this->getUser()->toId()->toUuid(),
                $model->description
            );

            $em->persist($transaction);

            if ($model->wallet instanceof Wallet) {
                $em->persist(
                    new WalletTransaction(
                        WalletTransactionId::generate(),
                        $model->wallet->toId(),
                        $money,
                        WalletTransactionSource::operandManual(),
                        $customerTransactionId->toUuid(),
                        null
                    )
                );
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
