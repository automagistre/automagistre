<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\OperandId;
use App\Customer\Enum\CustomerTransactionSource;
use App\Customer\Form\TransactionDto;
use App\EasyAdmin\Controller\AbstractController;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class TransactionController extends AbstractController
{
    protected function createNewEntity(): TransactionDto
    {
        $recipient = $this->getIdentifier(OperandId::class);

        $request = $this->request;

        if (!$request->query->has('type')) {
            throw new LogicException('Type required.');
        }

        $model = new TransactionDto();
        $model->recipient = $recipient;
        $model->increment = 'increment' === $request->query->getAlnum('type');

        $model->wallet = $this->registry->repository(Wallet::class)
            ->findOneBy(['defaultInManualTransaction' => true])
        ;

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
    protected function persistEntity($entity): void
    {
        $model = $entity;
        assert($model instanceof TransactionDto);

        $this->em->transactional(function (EntityManagerInterface $em) use ($model): void {
            /** @var Money $money */
            $money = $model->amount;
            $money = $model->increment ? $money->absolute() : $money->negative();

            $customerTransactionId = CustomerTransactionId::generate();

            if ($model->wallet instanceof Wallet) {
                $walletTransactionId = WalletTransactionId::generate();

                $em->persist(new CustomerTransaction(
                    $customerTransactionId,
                    $model->recipient,
                    $money,
                    CustomerTransactionSource::manual(),
                    $walletTransactionId->toUuid(),
                    $model->description,
                ));

                $em->persist(
                    new WalletTransaction(
                        $walletTransactionId,
                        $model->wallet->toId(),
                        $money,
                        WalletTransactionSource::operandManual(),
                        $customerTransactionId->toUuid(),
                        null,
                    ),
                );
            } else {
                $em->persist(new CustomerTransaction(
                    $customerTransactionId,
                    $model->recipient,
                    $money,
                    CustomerTransactionSource::manualWithoutWallet(),
                    $this->getUser()->toId()->toUuid(),
                    $model->description,
                ));
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
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $customerId = $this->getIdentifierOrNull(OperandId::class);

        if (null !== $customerId) {
            $qb->andWhere('entity.operandId = :operand')
                ->setParameter('operand', $customerId->toString())
            ;
        }

        $qb->orderBy('entity.created.at', 'DESC')
            ->addOrderBy('entity.id', 'DESC')
        ;

        return $qb;
    }
}
