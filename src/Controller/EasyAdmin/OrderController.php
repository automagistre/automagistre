<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Costil;
use App\Entity\Car;
use App\Entity\Employee;
use App\Entity\MotionOrder;
use App\Entity\Operand;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemGroup;
use App\Entity\OrderItemPart;
use App\Entity\OrderItemService;
use App\Entity\OrderNote;
use App\Entity\OrderPayment;
use App\Entity\Organization;
use App\Entity\Person;
use App\Enum\OrderStatus;
use App\Form\Model\Payment as PaymentModel;
use App\Form\Type\OrderItemServiceType;
use App\Form\Type\PaymentType;
use App\Manager\PaymentManager;
use App\Manager\ReservationManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Currency;
use Money\Money;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AbstractController
{
    /**
     * @var ReservationManager
     */
    private $reservationManager;

    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(ReservationManager $reservationManager, PaymentManager $paymentManager)
    {
        $this->reservationManager = $reservationManager;
        $this->paymentManager = $paymentManager;
    }

    public function suspendAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        $request = $this->request;
        $form = $this->createFormBuilder([
            'till' => $order->isSuspended() ? $order->getLastSuspend()->getTill() : new DateTimeImmutable(),
        ])
            ->add('till', DateType::class, [
                'required' => true,
                'label' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('reason', TextType::class, [
                'label' => 'Причина',
                'required' => true,
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var DateTimeImmutable $till */
            $till = $form->get('till')->getData();
            $reason = $form->get('reason')->getData();

            $order->suspend($till, $reason);
            $this->em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/suspend.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    public function statusAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        if (!$order->isEditable()) {
            $this->addFlash('error', 'Невозможно изменить статус у закрытого заказа.');

            return $this->redirectToReferrer();
        }

        $status = new OrderStatus($this->request->query->getInt('status'));
        if (!$status->isSelectable()) {
            $this->addFlash('error', 'Невозможно вручную установить указанный статус');

            return $this->redirectToReferrer();
        }

        $order->setStatus($status);
        $this->em->flush();

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritdoc}
     */
    public function isActionAllowed($actionName): bool
    {
        if (!\in_array($actionName, ['show', 'finish'], true) && null !== $id = $this->request->get('id')) {
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
                throw new LogicException(\sprintf('"%s" is required.', PaymentModel::class));
            }

            $this->handlePayment($model, $order);

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/payment.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    public function matchingAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required.');
        }

        $car = $order->getCar();
        if (!$car instanceof Car) {
            throw new BadRequestHttpException('Car required.');
        }

        [$servicePrice, $partPrice, $totalPrice] = $car->getRecommendationPrice();

        return $this->render('easy_admin/order_print/matching.html.twig', [
            'order' => $order,
            'car' => $car,
            'customer' => $order->getCustomer(),
            'recommendations' => $car->getRecommendations(),
            'totalRecommendationService' => $servicePrice,
            'totalRecommendationPart' => $partPrice,
            'totalRecommendationAll' => $totalPrice,
            'potentialPrice' => $order->getTotalPrice()->add($totalPrice),
        ]);
    }

    public function giveoutAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        return $this->render('easy_admin/order_print/giveout.html.twig', [
            'order' => $order,
            'car' => $order->getCar(),
            'services' => $order->getItems(OrderItemService::class),
            'parts' => $order->getItems(OrderItemPart::class),
        ]);
    }

    public function finishAction(): Response
    {
        $em = $this->em;
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if ($order->isClosed() || $order->isReadyToClose()) {
            goto finish;
        }

        /** @var OrderItemService[] $services */
        $services = $order->getServicesWithoutWorker();

        if ([] !== $services) {
            $form = $this->createForm(CollectionType::class, $services, [
                'label' => false,
                'entry_type' => OrderItemServiceType::class,
            ])
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                goto mileage;
            }

            return $this->render('easy_admin/order/finish.html.twig', [
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
                        : \sprintf('(предыдущий: %s)', $mileage)),
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

        if ($request->isMethod('POST')) {
            return $this->redirect($request->getUri());
        }

        $parameters = [
            'order' => $order,
            'groups' => \array_filter($order->getRootItems(), function (OrderItem $item) {
                return $item instanceof OrderItemGroup;
            }),
            'services' => \array_filter($order->getRootItems(), function (OrderItem $item) {
                return $item instanceof OrderItemService;
            }),
            'parts' => $order->getItems(OrderItemPart::class),
        ];

        $customer = $order->getCustomer();
        if ($customer instanceof Operand) {
            $parameters['customer'] = $customer;
            $parameters['title'] = $customer->getFullName();
        }

        $car = $order->getCar();
        if ($car instanceof Car) {
            $parameters['car'] = $car;

            $recommendations = [];
            foreach ($car->getRecommendations() as $recommendation) {
                $recommendations['items'][] = $recommendation;

                $totalServicePrice = $recommendations['totalServicePrice'] ?? new Money(0, new Currency('RUB'));
                $totalPartPrice = $recommendations['totalPartPrice'] ?? new Money(0, new Currency('RUB'));
                $totalPrice = $recommendations['totalPrice'] ?? new Money(0, new Currency('RUB'));

                $servicePrice = $recommendation->getPrice();
                $partPrice = $recommendation->getTotalPartPrice();

                $totalServicePrice = $totalServicePrice->add($servicePrice);
                $totalPartPrice = $totalPartPrice->add($partPrice);

                $recommendations['totalServicePrice'] = $totalServicePrice;
                $recommendations['totalPartPrice'] = $totalPartPrice;
                $recommendations['totalPrice'] = $totalPrice->add($servicePrice)->add($partPrice);
            }

            if ([] !== $recommendations) {
                $parameters['recommendations'] = $recommendations;
            }
        }

        return $this->render('easy_admin/order_print/final.html.twig', $parameters);
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

            return $this->render('easy_admin/order/payment.html.twig', [
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        close:

        $em->transactional(function (EntityManagerInterface $em) use ($order): void {
            $em->refresh($order);
            $order->close($this->getUser());

            $customer = $order->getCustomer();
            if ($customer instanceof Operand) {
                $description = \sprintf('# Списание по заказу #%s', $order->getId());

                $this->paymentManager->createPayment($customer, $description, $order->getTotalPrice()->negative());
            }

            foreach ($order->getItems(OrderItemPart::class) as $item) {
                /* @var OrderItemPart $item */

                $part = $item->getPart();
                $quantity = $item->getQuantity();

                if (0 !== $this->reservationManager->reserved($item)) {
                    $this->reservationManager->deReserve($item, $quantity);
                }

                $em->persist(new MotionOrder($part, $quantity, $order));
            }

            foreach ($order->getItems(OrderItemService::class) as $item) {
                /** @var OrderItemService $item */
                $worker = $item->getWorker();
                $employee = $em->getRepository(Employee::class)->findOneBy(['person' => $worker]);

                if (!$employee instanceof Employee) {
                    $this->addFlash('warning', \sprintf(
                        'Для исполнителя "%s" нет записи работника, зарплата по заказу не начислена.',
                        $worker->getFullName()
                    ));

                    continue;
                }

                $price = $item->getPrice();
                if (!$price->isPositive()) {
                    continue;
                }

                $salary = $price->multiply($employee->getRatio() / 100);
                $description = \sprintf('# ЗП %s по заказу #%s', $worker->getFullName(), $order->getId());

                $this->paymentManager->createPayment($worker, $description, $salary->absolute());
            }
        });

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $em = $this->em;
            $entity = $parameters['entity'];

            $parameters['notes'] = $em->getRepository(OrderNote::class)
                ->findBy(['order' => $entity], ['createdAt' => 'DESC']);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Order
    {
        $entity = new Order();

        $customer = $this->getEntity(Operand::class);
        if ($customer instanceof Operand) {
            $entity->setCustomer($customer);
        }

        $car = $this->getEntity(Car::class);
        if ($car instanceof Car) {
            $entity->setCar($car);
        }

        return $entity;
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

        $customer = $this->getEntity(Operand::class);
        if ($customer instanceof Operand) {
            $qb->andWhere('entity.customer = :customer')
                ->setParameter('customer', $customer);
        }

        $car = $this->getEntity(Car::class);
        if ($car instanceof Car) {
            $qb->andWhere('entity.car = :car')
                ->setParameter('car', $car);
        }

        return $qb;
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
            ->leftJoin('carModel.manufacturer', 'manufacturer')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class);

        foreach (\explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('car.gosnomer', $key),
                $qb->expr()->like('carModel.name', $key),
                $qb->expr()->like('carModel.localizedName', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('manufacturer.localizedName', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        $qb
            ->orderBy('orders.closedAt', 'ASC')
            ->addOrderBy('orders.id', 'DESC');

        return $qb;
    }

    /**
     * @param Order $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->setReferer($this->generateUrl('easyadmin', [
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
            foreach ([Costil::CASHBOX => $model->amountCash, Costil::ACCOUNT => $model->amountNonCash] as $id => $money) {
                /** @var Money $money */
                if (!$money->isPositive()) {
                    continue;
                }

                if (null !== $model->recipient) {
                    $payment = $this->paymentManager->createPayment($model->recipient, $model->description, $money);
                    $em->persist(new OrderPayment($order, $payment));
                }

                $cashbox = $em->getRepository(Operand::class)->find($id);
                $this->paymentManager->createPayment($cashbox, $model->description, $money);
            }
        });
    }
}
