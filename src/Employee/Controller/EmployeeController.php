<?php

declare(strict_types=1);

namespace App\Employee\Controller;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\Person;
use App\Customer\Enum\CustomerTransactionSource;
use App\EasyAdmin\Controller\AbstractController;
use App\Employee\Entity\Employee;
use App\Employee\Entity\SalaryView;
use App\Employee\Event\EmployeeCreated;
use App\Employee\Event\EmployeeFired;
use App\Form\Type\MoneyType;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use function assert;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use LogicException;
use Money\Money;
use function sprintf;
use stdClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $request = $this->request;

        $recipient = $this->getEntity(Person::class);
        if (!$recipient instanceof Person) {
            throw new BadRequestHttpException('Person required.');
        }

        $model = new stdClass();
        $model->recipient = $recipient;
        $model->wallet = null;
        $model->amount = null;
        $model->description = null;

        $form = $this->createFormBuilder($model)
            ->add('recipient', EasyAdminAutocompleteType::class, [
                'label' => 'Получатель',
                'class' => Person::class,
                'disabled' => true,
            ])
            ->add('wallet', EntityType::class, [
                'label' => 'Счет списания',
                'class' => Wallet::class,
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
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->transactional(function (EntityManagerInterface $em) use ($model, $recipient): void {
                /** @var Money $money */
                $money = $model->amount;
                $money = $money->negative();

                $customerTransactionId = CustomerTransactionId::generate();
                $walletTransactionId = WalletTransactionId::generate();

                $em->persist(
                    new CustomerTransaction(
                        $customerTransactionId,
                        $recipient->toId(),
                        $money,
                        CustomerTransactionSource::payroll(),
                        $walletTransactionId->toUuid(),
                        $model->description
                    )
                );

                $em->persist(
                    new WalletTransaction(
                        $walletTransactionId,
                        $model->wallet->toId(),
                        $money,
                        WalletTransactionSource::payroll(),
                        $customerTransactionId->toUuid(),
                        null,
                    )
                );
            });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => sprintf('Выдача зарплаты - "%s"', $recipient->getFullName()),
            'button' => 'Выдать зарплату',
            'form' => $form->createView(),
        ]);
    }

    public function penaltyAction(): Response
    {
        $request = $this->request;

        $recipient = $this->getEntity(Person::class);
        if (!$recipient instanceof Person) {
            throw new BadRequestHttpException('Person required.');
        }

        $model = new stdClass();
        $model->recipient = $recipient;
        $model->wallet = null;
        $model->amount = null;
        $model->description = null;

        $form = $this->createFormBuilder($model)
            ->add('recipient', EasyAdminAutocompleteType::class, [
                'label' => 'Получатель',
                'class' => Person::class,
                'disabled' => true,
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Сумма',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => true,
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->transactional(function (EntityManagerInterface $em) use ($model, $recipient): void {
                $description = sprintf(
                    '# Оштрафован "%s" по причине "%s"',
                    $recipient->getFullName(),
                    $model->description
                );

                /** @var Money $money */
                $money = $model->amount;

                $em->persist(
                    new CustomerTransaction(
                        CustomerTransactionId::generate(),
                        $recipient->toId(),
                        $money,
                        CustomerTransactionSource::penalty(),
                        $this->getUser()->toId()->toUuid(),
                        $model->{$description},
                    )
                );
            });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => sprintf('Оштрафовать - "%s"', $recipient->getFullName()),
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
