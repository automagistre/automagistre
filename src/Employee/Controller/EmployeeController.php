<?php

declare(strict_types=1);

namespace App\Employee\Controller;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\OperandId;
use App\Customer\Enum\CustomerTransactionSource;
use App\EasyAdmin\Controller\AbstractController;
use App\Employee\Entity\Employee;
use App\Employee\Entity\SalaryView;
use App\Employee\Event\EmployeeCreated;
use App\Employee\Event\EmployeeFired;
use App\Employee\Form\PayoutDto;
use App\Form\Type\MoneyType;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use App\Wallet\Form\WalletType;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Money\Money;
use function sprintf;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeController extends AbstractController
{
    public function salaryAction(): Response
    {
        $recipientId = $this->getIdentifier(OperandId::class);
        if (!$recipientId instanceof OperandId) {
            throw new BadRequestHttpException('Person required.');
        }

        $model = new PayoutDto();
        $model->recipient = $recipientId;

        $form = $this->createFormBuilder($model)
            ->add('walletId', WalletType::class, [
                'required' => true,
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Сумма',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => false,
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->transactional(static function (EntityManagerInterface $em) use ($model, $recipientId): void {
                $customerTransactionId = CustomerTransactionId::generate();
                $walletTransactionId = WalletTransactionId::generate();

                $em->persist(
                    new CustomerTransaction(
                        $customerTransactionId,
                        $recipientId,
                        $model->amount->negative(),
                        CustomerTransactionSource::payroll(),
                        $walletTransactionId->toUuid(),
                        $model->description
                    )
                );

                $em->persist(
                    new WalletTransaction(
                        $walletTransactionId,
                        $model->walletId,
                        $model->amount->negative(),
                        WalletTransactionSource::payroll(),
                        $customerTransactionId->toUuid(),
                        null,
                    )
                );
            });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => sprintf('Выдача зарплаты - "%s"', $this->display($recipientId)),
            'button' => 'Выдать зарплату',
            'form' => $form->createView(),
        ]);
    }

    public function penaltyAction(): Response
    {
        $personId = $this->getIdentifier(OperandId::class);
        if (!$personId instanceof OperandId) {
            throw new BadRequestHttpException('Person required.');
        }

        $model = new stdClass();
        $model->recipient = $personId;
        $model->wallet = null;
        $model->amount = null;
        $model->description = null;

        $form = $this->createFormBuilder($model)
            ->add('amount', MoneyType::class, [
                'label' => 'Сумма',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => true,
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->transactional(function (EntityManagerInterface $em) use ($model, $personId): void {
                $description = sprintf(
                    '# Оштрафован "%s" по причине "%s"',
                    $this->display($personId),
                    $model->description
                );

                /** @var Money $money */
                $money = $model->amount;

                $em->persist(
                    new CustomerTransaction(
                        CustomerTransactionId::generate(),
                        $personId,
                        $money->negative(),
                        CustomerTransactionSource::penalty(),
                        $this->getUser()->toId()->toUuid(),
                        $model->{$description},
                    )
                );
            });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => sprintf('Оштрафовать - "%s"', $this->display($personId)),
            'button' => 'Оштрафовать',
            'form' => $form->createView(),
        ]);
    }

    public function fireAction(): Response
    {
        $entity = $this->findCurrentEntity();
        if (!$entity instanceof Employee) {
            throw new LogicException('Employee required.');
        }

        if ($entity->isFired()) {
            $this->addFlash('error', sprintf('Работник "%s" уже уволен', $this->display($entity->toPersonId())));

            return $this->redirectToReferrer();
        }

        $entity->fire();
        $this->em->flush();

        $this->event(new EmployeeFired($entity));

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Employee);

        parent::persistEntity($entity);

        $this->event(new EmployeeCreated($entity));
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            /** @var Employee $entity */
            $entity = $parameters['entity'];

            $parameters['salaries'] = $this->registry->repository(SalaryView::class)
                ->findBy(['employeeId' => $entity->toId()]);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
