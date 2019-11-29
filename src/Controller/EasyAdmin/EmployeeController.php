<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Employee;
use App\Entity\Tenant\MonthlySalary;
use App\Entity\Tenant\OperandTransaction;
use App\Entity\Tenant\Penalty;
use App\Entity\Tenant\Salary;
use App\Entity\Tenant\Wallet;
use App\Entity\Tenant\WalletTransaction;
use App\Event\EmployeeCreated;
use App\Event\EmployeeFined;
use App\Event\EmployeeFired;
use App\Event\EmployeeSalaryIssued;
use App\Form\Type\MoneyType;
use App\Manager\PaymentManager;
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
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

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
            $em = $this->em;
            $salary = $em->transactional(function (EntityManagerInterface $em) use ($model, $recipient): Salary {
                $description = sprintf('# Выдача зарплаты "%s"', $recipient->getFullName());
                if (null !== $model->description) {
                    $description .= sprintf(' Комментарий - "%s"', $model->description);
                }

                /** @var Money $money */
                $money = $model->amount;

                $outcome = $this->paymentManager->createPayment($model->wallet, $description, $money->negative());
                if (!$outcome instanceof WalletTransaction) {
                    throw new LogicException('WalletTransaction expected.');
                }
                $income = $this->paymentManager->createPayment($recipient, $description, $money->negative());
                if (!$income instanceof OperandTransaction) {
                    throw new LogicException('OperandTransaction expected.');
                }

                $salary = new Salary($income, $outcome, $model->description);
                $em->persist($salary);

                return $salary;
            });

            $this->event(new EmployeeSalaryIssued($salary));

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
            $em = $this->em;
            $penalty = $em->transactional(function (EntityManagerInterface $em) use ($model, $recipient): Penalty {
                $description = sprintf(
                    '# Оштрафован "%s" по причине "%s"',
                    $recipient->getFullName(),
                    $model->description
                );

                /** @var Money $money */
                $money = $model->amount;

                $transaction = $this->paymentManager->createPayment($recipient, $description, $money->negative());
                if (!$transaction instanceof OperandTransaction) {
                    throw new LogicException('OperandTransaction expected.');
                }

                $penalty = new Penalty($transaction, $model->description);
                $em->persist($penalty);

                return $penalty;
            });

            $this->event(new EmployeeFined($penalty));

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
            $this->addFlash('error', sprintf('Сотрудник "%s" уже уволен', (string) $entity));

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

            $registry = $this->container->get(Registry::class);

            $parameters['monthlySalaries'] = $registry->repository(MonthlySalary::class)
                ->findBy(['employee' => $entity]);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
