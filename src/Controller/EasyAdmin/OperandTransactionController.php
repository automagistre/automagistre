<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Wallet;
use App\Manager\PaymentManager;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use stdClass;
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

    protected function createNewEntity(): stdClass
    {
        $recipient = $this->getEntity(Operand::class);
        if (!$recipient instanceof Operand) {
            throw new LogicException('Operand required.');
        }

        $obj = new stdClass();
        $obj->id = null;
        $obj->recipient = $recipient;
        $obj->description = null;
        $obj->amount = null;
        $obj->wallet = $this->em->getRepository(Wallet::class)
            ->findOneBy(['defaultInManualTransaction' => true]);

        return $obj;
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
     * @param stdClass $entity
     */
    protected function persistEntity($entity): void
    {
        $this->em->transactional(function () use ($entity): void {
            /** @var Money $money */
            $money = $entity->amount;

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
            $qb->andWhere('entity.recipient = :recipient')
                ->setParameter('recipient', $recipient);
        }

        $qb->orderBy('entity.createdAt', 'DESC')
            ->addOrderBy('entity.id', 'DESC');

        return $qb;
    }
}
