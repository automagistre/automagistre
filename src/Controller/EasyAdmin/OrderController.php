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
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use LogicException;
use Money\Money;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

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
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->get('payment')->getData();
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

    public function closeAction(): Response
    {
        $em = $this->em;
        $factory = $this->formFactory;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        $form = $this->createFormBuilder(null, ['label' => false]);

        if ($order->getTotalForPayment()->isPositive()) {
            $form->add($this->createPaymentForm($order));
        }

        /** @var OrderItemService[] $services */
        $services = $order->getServicesWithoutWorker();

        if ([] !== $services) {
            $form->add($factory->createNamedBuilder('services', CollectionType::class, $services, [
                'label' => false,
                'entry_type' => WorkerType::class,
                'entry_options' => [
                    'label' => false,
                ],
            ]));
        }

        $car = $order->getCar();
        if (null !== $car && null === $order->getMileage()) {
            $form->add($factory->createNamedBuilder('mileage', IntegerType::class, null, [
                'label' => sprintf('Пробег (предыдущий: %s)', $car->getMileage()),
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => $car->getMileage(),
                    ]),
                ],
            ]));
        }

        $form = $form->getForm()->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('mileage')) {
                $order->setMileage($form->get('mileage')->getData());
            }

            if ($form->has('payment')) {
                $this->handlePayment($form->get('payment')->getData(), $order);
            }

            $em->refresh($order);

            if ($order->getTotalForPayment()->isPositive()) {
                $this->addFlash('error', 'Заказ оплачен не полностью!');
            } elseif ([] !== $order->getServicesWithoutWorker()) {
                $this->addFlash('error', 'Есть работы без исполнителя!');
            } else {
                $em->transactional(function (EntityManagerInterface $em) use ($order): void {
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
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/close.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
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
        $this->get('event_dispatcher')->addListener(EasyAdminEvents::POST_PERSIST, function (GenericEvent $event
        ): void {
            /** @var Order $entity */
            $entity = $event->getArgument('entity');

            $this->request->query->set('referer', $this->generateUrl('easyadmin', [
                'entity' => 'Order',
                'action' => 'show',
                'id' => $entity->getId(),
            ]));
        });

        parent::persistEntity($entity);
    }

    private function createPaymentForm(Order $order): FormBuilderInterface
    {
        $model = new PaymentModel();
        $model->recipient = $order->getCustomer();
        $model->description = '# Начисление по заказу #'.$order->getId();
        $model->amount = $order->getTotalForPayment();

        $factory = $this->formFactory;

        return $factory->createNamedBuilder('payment', PaymentType::class, $model, [
            'disable_recipient' => true,
            'label' => false,
        ]);
    }

    private function handlePayment(PaymentModel $model, Order $order): void
    {
        $em = $this->em;

        $em->transactional(function (EntityManagerInterface $em) use ($model, $order): void {
            if (null !== $model->recipient) {
                $this->createPayment($model->recipient, $model->description, $model->amount);
            }

            $id = 'cash' === $model->paymentType ? COSTIL_CASSA : COSTIL_BEZNAL;

            $cashbox = $em->getRepository(Operand::class)->find($id);
            $payment = $this->createPayment($cashbox, $model->description, $model->amount);

            $em->persist(new OrderPayment($order, $payment));
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

        /** @var Payment $lastPayment */
        $lastPayment = $em->createQueryBuilder()
            ->select('payment')
            ->from(Payment::class, 'payment')
            ->where('payment.recipient = :recipient')
            ->orderBy('payment.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getOneOrNullResult();

        return $lastPayment->getSubtotal()->add($money);
    }
}
