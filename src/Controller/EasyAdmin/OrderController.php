<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Employee;
use App\Entity\Operand;
use App\Entity\Order;
use App\Entity\OrderItemService;
use App\Entity\OrderPayment;
use App\Entity\Organization;
use App\Entity\Payment;
use App\Entity\Person;
use App\Form\Model\Payment as PaymentModel;
use App\Form\Type\PaymentType;
use App\Form\Type\WorkerType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

const COSTIL_CASSA = 1;
const COSTIL_BEZNAL = 2422;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function isActionAllowed($actionName): bool
    {
        if ('show' !== $actionName && null !== $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(Order::class)->find($id);

            return $entity->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    public function paymentAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        $form = $this->createPaymentForm($order)
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            if (!$model instanceof PaymentModel) {
                throw new LogicException(sprintf('"%s" is required.', PaymentModel::class));
            }

            $this->handlePayment($model, $order);

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/payment.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    public function printAction(): Response
    {
        $this->addFlash('success', 'Абракадабра тестовая печать!');

        return $this->redirectToEasyPath($this->getEntity(Order::class), 'show');
    }

    public function finishAction(): Response
    {
        $em = $this->em;
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if ($order->isReadyToClose()) {
            goto finish;
        }

        /** @var OrderItemService[] $services */
        $services = $order->getServicesWithoutWorker();

        if ([] !== $services) {
            $form = $this->createForm(CollectionType::class, $services, [
                'label' => false,
                'entry_type' => WorkerType::class,
                'entry_options' => [
                    'label' => false,
                ],
            ])
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                goto mileage;
            }

            return $this->render('easy_admin/order/finish_worker.html.twig', [
                'header' => 'Введите исполнителей',
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        mileage:

        $car = $order->getCar();
        if (null !== $car && null === $order->getMileage()) {
            $mileage = $car->getMileage();

            $form = $this->createForm(IntegerType::class, null, [
                'label' => 'Пробег '.(null === $mileage
                        ? '(предыдущий отсутствует)'
                        : sprintf('(предыдущий: %s)', $mileage)),
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => $mileage ?? 0,
                    ]),
                ],
            ])
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $order->setMileage($form->getData());
                $em->flush();

                goto finish;
            }

            return $this->render('easy_admin/order/finish.html.twig', [
                'header' => 'Введите пробег',
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        finish:

        $this->addFlash('success', 'Представь что ты увидел файл на финишную печать');

        return $this->redirectToEasyPath($this->getEntity(Order::class), 'show');
    }

    public function closeAction(): Response
    {
        $em = $this->em;
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if (!$order->isReadyToClose()) {
            if ([] !== $order->getServicesWithoutWorker()) {
                $this->addFlash('error', 'Есть работы без исполнителя!');
            }

            if (null === $order->getMileage()) {
                $this->addFlash('error', 'Пробег не указан!');
            }

            return $this->redirectToEasyPath($order, 'show');
        }

        $step = $request->query->get('step');
        if (null === $step) {
            return $this->render('easy_admin/order/close.html.twig', [
                'order' => $order,
            ]);
        }

        if ('paid' === $step) {
            $form = $this->createPaymentForm($order)
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->handlePayment($form->getData(), $order);

                goto close;
            }

            return $this->render('easy_admin/order/close_payment.html.twig', [
                'header' => 'Создать платёж',
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        close:

        $em->transactional(function (EntityManagerInterface $em) use ($order): void {
            $em->refresh($order);
            $order->close();

            $customer = $order->getCustomer();
            if ($customer instanceof Operand) {
                $description = sprintf('# Списание по заказу #%s', $order->getId());

                $this->createPayment($customer, $description, $order->getTotalPrice()->negative());
            }

            foreach ($order->getItems(OrderItemService::class) as $item) {
                /** @var OrderItemService $item */
                $worker = $item->getWorker();
                $employee = $em->getRepository(Employee::class)->findOneBy(['person' => $worker]);

                $salary = $item->getPrice()->multiply($employee->getRatio() / 100);
                $description = sprintf('# ЗП %s по заказу #%s', $worker->getFullName(), $order->getId());

                $this->createPayment($worker, $description, $salary->absolute());
            }
        });

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->em->getRepository(Order::class)->createQueryBuilder('orders')
            ->leftJoin('orders.customer', 'customer')
            ->leftJoin('orders.car', 'car')
            ->leftJoin('car.carModel', 'carModel')
            ->leftJoin('car.carModification', 'carModification')
            ->leftJoin('carModel.manufacturer', 'manufacturer')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class);

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('car.gosnomer', $key),
                $qb->expr()->like('carModel.name', $key),
                $qb->expr()->like('carModification.name', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        return $qb;
    }

    /**
     * @param Order $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->request->query->set('referer', $this->generateUrl('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $entity->getId(),
        ]));
    }

    private function createPaymentForm(Order $order): FormInterface
    {
        $model = new PaymentModel();
        $model->recipient = $order->getCustomer();
        $model->description = '# Начисление по заказу #'.$order->getId();
        $forPayment = $order->getTotalForPayment();
        $model->amountCash = $forPayment->isPositive() ? $forPayment : new Money(0, $forPayment->getCurrency());
        $model->amountNonCash = new Money(0, $forPayment->getCurrency());

        return $this->createForm(PaymentType::class, $model, [
            'disable_recipient' => true,
            'disable_description' => true,
            'label' => false,
        ]);
    }

    private function handlePayment(PaymentModel $model, Order $order): void
    {
        $em = $this->em;

        $em->transactional(function (EntityManagerInterface $em) use ($model, $order): void {
            foreach ([COSTIL_CASSA => $model->amountCash, COSTIL_BEZNAL => $model->amountNonCash] as $id => $money) {
                /** @var Money $money */
                if (!$money->isPositive()) {
                    continue;
                }

                if (null !== $model->recipient) {
                    $this->createPayment($model->recipient, $model->description, $money);
                }

                $cashbox = $em->getRepository(Operand::class)->find($id);
                $payment = $this->createPayment($cashbox, $model->description, $money);

                $em->persist(new OrderPayment($order, $payment));
            }
        });
    }

    private function createPayment(Operand $recipient, string $description, Money $money): Payment
    {
        $em = $this->em;

        return $em->transactional(function (EntityManagerInterface $em) use ($recipient, $description, $money) {
            $payment = new Payment(
                $recipient,
                $description,
                $money,
                $this->calcSubtotal($recipient, $money)
            );

            $em->persist($payment);

            return $payment;
        });
    }

    private function calcSubtotal(Operand $recipient, Money $money): Money
    {
        $em = $this->em;

        /** @var Payment|null $lastPayment */
        $lastPayment = $em->createQueryBuilder()
            ->select('payment')
            ->from(Payment::class, 'payment')
            ->where('payment.recipient = :recipient')
            ->orderBy('payment.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $lastPayment) {
            return $money;
        }

        return $lastPayment->getSubtotal()->add($money);
    }
}
