<?php

declare(strict_types=1);

namespace App\Expense\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Expense\Entity\Expense;
use App\Expense\Entity\ExpenseId;
use App\Expense\Form\ExpenseItemDto;
use App\Expense\Form\ExpenseType;
use App\Form\Type\MoneyType;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use App\Wallet\Form\WalletType;
use function assert;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExpenseController extends AbstractController
{
    public function createItemAction(): Response
    {
        $dto = new ExpenseItemDto();
        /** @var ExpenseId $expenseId */
        $expenseId = $this->getIdentifier(ExpenseId::class);
        $dto->expenseId = $expenseId;

        $form = $this->createFormBuilder($dto)
            ->add('expenseId', ExpenseType::class, [
                'required' => true,
            ])
            ->add('walletId', WalletType::class, [
                'label' => 'Счёт списания',
                'required' => true,
                'expanded' => true,
            ])
            ->add('money', MoneyType::class, [
                'label' => 'Сумма',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->persist(
                new WalletTransaction(
                    WalletTransactionId::generate(),
                    $dto->walletId,
                    $dto->money->negative(),
                    WalletTransactionSource::expense(),
                    $dto->expenseId->toUuid(),
                    $dto->description,
                )
            );

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Создать расход',
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new Expense($model->name);

        parent::persistEntity($entity);
    }
}
