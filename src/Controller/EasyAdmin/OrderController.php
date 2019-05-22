<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarModel;
use App\Entity\Landlord\MC\Line;
use App\Entity\Landlord\Operand;
use App\Entity\Landlord\Organization;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Order;
use App\Entity\Tenant\OrderItem;
use App\Entity\Tenant\OrderItemPart;
use App\Entity\Tenant\OrderItemService;
use App\Entity\Tenant\OrderNote;
use App\Entity\Tenant\Wallet;
use App\Enum\OrderStatus;
use App\Events;
use App\Form\Model\OrderTOService;
use App\Form\Type\MoneyType;
use App\Form\Type\OrderItemServiceType;
use App\Form\Type\OrderTOServiceType;
use App\Manager\OrderManager;
use App\Manager\PaymentManager;
use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use LogicException;
use Money\Currency;
use Money\Money;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AbstractController
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    /**
     * @var OrderManager
     */
    private $orderManager;

    public function __construct(PaymentManager $paymentManager, OrderManager $orderManager)
    {
        $this->paymentManager = $paymentManager;
        $this->orderManager = $orderManager;
    }

    public function TOAction(): Response
    {
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        $car = $order->getCar();
        if (!$car instanceof Car) {
            throw new LogicException('Car required.');
        }

        $carModel = $car->getCarModel();
        if (!$carModel instanceof CarModel) {
            throw new LogicException('CarModel required.');
        }

        if (!$car->equipment->isFilled()) {
            $this->addFlash('warning', 'Для отображения карты ТО необходимо заполнить комплектацию.');

            return $this->redirectToEasyPath($car, 'edit', [
                'order_id' => $order->getId(),
                'validate' => 'equipment',
            ]);
        }

        $registry = $this->container->get(Registry::class);

        $qb = $registry->repository(Line::class)
            ->createQueryBuilder('line')
            ->join('line.equipment', 'equipment')
            ->where('equipment.model = :model')
            ->andWhere('equipment.equipment.engine.name = :engine')
            ->andWhere('equipment.equipment.engine.capacity = :capacity')
            ->andWhere('equipment.equipment.transmission = :transmission')
            ->andWhere('equipment.equipment.wheelDrive = :wheelDrive')
            ->setParameters([
                'model' => $car->getCarModel(),
                'engine' => $car->equipment->engine->name,
                'capacity' => $car->equipment->engine->capacity,
                'transmission' => $car->equipment->transmission,
                'wheelDrive' => $car->equipment->wheelDrive,
            ]);

        $periods = (clone $qb)
            ->select('line.period')
            ->groupBy('line.period')
            ->getQuery()
            ->getArrayResult();
        $periods = \array_map('array_shift', $periods);

        if (0 === \count($periods)) {
            $this->addFlash('warning', \sprintf('Карт ТО для "%s" не найдео.', $car->toString(true)));

            return $this->redirectToReferrer();
        }

        $currentPeriod = $request->query->getInt('period');
        if (!\in_array($currentPeriod, $periods, true)) {
            $currentPeriod = $periods[0];
        }

        /** @var OrderTOService[] $services */
        $services = [];

        /** @var Line[] $lines */
        $lines = $qb->getQuery()->getResult();
        foreach ($lines as $line) {
            if (0 !== $currentPeriod % $line->period) {
                continue;
            }

            $services[$line->getId()] = OrderTOService::from($line);
        }

        $form = $this->createFormBuilder(['services' => $services])
            ->add('services', CollectionType::class, [
                'label' => false,
                'entry_type' => OrderTOServiceType::class,
                'allow_add' => false,
                'allow_delete' => false,
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $registry->manager(OrderItem::class);
            foreach ($services as $service) {
                if (!$service->selected) {
                    continue;
                }

                $orderItemService = new OrderItemService(
                    $order,
                    $service->service,
                    $service->price,
                    $this->getUser()
                );
                $em->persist($orderItemService);

                foreach ($service->parts as $part) {
                    if (!$part->selected) {
                        continue;
                    }

                    $orderItemPart = new OrderItemPart(
                        $order,
                        $part->part,
                        $part->quantity,
                        $part->price,
                        $this->getUser()
                    );
                    $em->persist($orderItemPart);

                    $orderItemPart->setParent($orderItemService);
                }
            }

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/to.html.twig', [
            'order' => $order,
            'car' => $car,
            'periods' => $periods,
            'currentPeriod' => $currentPeriod,
            'form' => $form->createView(),
        ]);
    }

    public function info(Order $order, bool $statusSelector = false): Response
    {
        $customer = $order->getCustomer();

        $balance = null;
        if ($customer instanceof Operand) {
            $balance = $order->isClosed() ? $order->getClosedBalance() : $this->paymentManager->balance($customer);
        }

        return $this->render('easy_admin/order/includes/main_information.html.twig', [
            'order' => $order,
            'status_selector' => $statusSelector,
            'balance' => $balance,
            'totalForPayment' => $order->getTotalForPayment($balance),
        ]);
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

            $order->suspend($till, $reason, $this->getUser());
            $this->em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/suspend.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    public function appointmentAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        $request = $this->request;
        $form = $this->createFormBuilder()
            ->add('date', DateTimeType::class, [
                'label' => 'Дата',
                'required' => true,
                'minutes' => [0, 30],
                'hours' => \range(9, 23),
                'input' => 'datetime_immutable',
                'model_timezone' => 'GMT+3',
                'view_timezone' => 'GMT+3',
                'data' => new DateTimeImmutable('now', new \DateTimeZone('GMT+3')),
                'constraints' => [
                    new GreaterThan('now GMT+3'),
                ],
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var DateTimeImmutable $date */
            $date = $form->get('date')->getData();

            $order->appointment($date);
            $order->setStatus(OrderStatus::scheduling());
            $this->em->flush();

            $this->event(Events::ORDER_APPOINTMENT, new GenericEvent($order, [
                'date' => $date->format(DATE_RFC3339),
            ]));

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/appointment.html.twig', [
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

        $this->event(Events::ORDER_STATUS, new GenericEvent($order, ['status' => $status]));

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritdoc}
     */
    public function isActionAllowed($actionName): bool
    {
        if (!\in_array($actionName, ['show', 'finish', 'act'], true) && null !== $id = $this->request->get('id')) {
            $registry = $this->container->get(Registry::class);

            $entity = $registry->repository(Order::class)->find($id);

            return $entity->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    public function paymentAction(): Response
    {
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if (!$this->canReceivePayments()) {
            return $this->redirectToEasyPath('Wallet', 'new', ['referer' => $request->getUri()]);
        }

        $form = $this->createPaymentForm($order)
            ->add('desc', TextType::class, [
                'label' => 'Описание',
                'mapped' => false,
                'required' => false,
            ])
            ->handleRequest($this->request);

        $model = $form->getData();
        $model->description = \sprintf('# Аванс по заказу #%s', $order->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $model->recipient = null;

            $this->em->transactional(function () use ($order, $model, $form): void {
                /** @var Money $payment */
                foreach ($model->wallets as ['payment' => $payment]) {
                    if (!$payment->isPositive()) {
                        continue;
                    }

                    $order->addPayment($payment, $form->get('desc')->getData(), $this->getUser());
                }

                $this->handlePayment($model);
            });

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

        return $this->render('easy_admin/order_print/matching_v2.html.twig', [
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

        return $this->render('easy_admin/order_print/giveout_v2.html.twig', [
            'order' => $order,
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
                'label' => 'Пробег '.(0 === $mileage
                        ? '(предыдущий отсутствует)'
                        : \sprintf('(предыдущий: %s)', $mileage)),
                'constraints' => [
                    new Assert\GreaterThan([
                        'value' => 2000,
                        'message' => 'Пидр иди смотри пробег',
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

        if ($request->isMethod('POST')) {
            return $this->redirect($request->getUri());
        }

        $parameters = [
            'order' => $order,
        ];

        $customer = $order->getCustomer();
        if ($customer instanceof Operand) {
            $parameters['customer'] = $customer;
            $parameters['title'] = $customer->getFullName();

            $parameters['balance'] = $order->isClosed()
                ? $order->getClosedBalance()
                : $this->paymentManager->balance($customer);
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

        $version = $request->query->getInt('version');
        $template = \sprintf('easy_admin/order_print/final_v%s.html.twig', $version);

        return $this->render($template, $parameters);
    }

    public function actAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        if (!$order->isClosed()) {
            $this->addFlash('error', 'Печать акта возможно только после закрытия заказа.');

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order_print/act.html.twig', [
            'order' => $order,
        ]);
    }

    public function closeAction(): Response
    {
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

        $customer = $order->getCustomer();
        $balance = $customer instanceof Operand ? $this->paymentManager->balance($customer) : null;

        if (!$order->getTotalForPayment($balance)->isPositive()) {
            goto close;
        }

        $step = $request->query->get('step');
        if (null === $step) {
            return $this->render('easy_admin/order/close.html.twig', [
                'order' => $order,
            ]);
        }

        if ('paid' === $step) {
            if (!$this->canReceivePayments()) {
                return $this->redirectToEasyPath('Wallet', 'new', ['referer' => $request->getUri()]);
            }

            $form = $this->createPaymentForm($order)
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->handlePayment($form->getData());

                goto close;
            }

            return $this->render('easy_admin/order/payment.html.twig', [
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        close:

        $this->orderManager->close($order);

        $car = $order->getCar();
        if ($car instanceof Car) {
            $car->setMileage($order->getMileage());
            $registry = $this->container->get(Registry::class);

            $registry->manager(Car::class)->flush();
        }

        $this->event(Events::ORDER_CLOSED, new GenericEvent($order));

        $this->addFlash('success', \sprintf('Заказ №%s закрыт', $order->getId()));

        return $this->redirectToEasyPath($order, 'show');
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
        $entity = parent::createNewEntity();
        if (!$entity instanceof Order) {
            throw new LogicException('Order expected');
        }

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
            $qb->andWhere('entity.customer.id = :customer')
                ->setParameter('customer', $customer->getId());
        }

        $car = $this->getEntity(Car::class);
        if ($car instanceof Car) {
            $qb->andWhere('entity.car.id = :car')
                ->setParameter('car', $car->getId());
        }

        $request = $this->request;
        if (null === $customer && null === $car && !$request->query->has('all')) {
            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->neq('entity.status', ':closedStatus'),
                    $qb->expr()->eq('DATE(entity.closedAt)', ':today')
                )
            )
                ->setParameter('closedStatus', OrderStatus::closed())
                ->setParameter('today', (new \DateTime())->format('Y-m-d'));
        }

        // EAGER Loading
        $qb
            ->select(['entity', 'items', 'suspends'])
            ->leftJoin('entity.items', 'items')
            ->leftJoin('entity.suspends', 'suspends');

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
        $registry = $this->container->get(Registry::class);

        $qb = $registry->repository(Car::class)
            ->createQueryBuilder('car')
            ->select('car.id AS car_id')
            ->addSelect('customer.id AS operand_id')
            ->leftJoin('car.owner', 'customer')
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
                $qb->expr()->like('carModel.caseName', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('manufacturer.localizedName', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        $cars = [];
        $customers = [];
        foreach ($qb->getQuery()->getArrayResult() as $item) {
            ['car_id' => $carId, 'operand_id' => $customerId] = $item;
            $cars[] = $carId;
            $customers[] = $customerId;
        }

        return $registry->repository(Order::class)
            ->createQueryBuilder('entity')
            ->where('entity.car.id IN (:car)')
            ->orWhere('entity.customer.id IN (:customer)')
            ->setParameter('car', $cars)
            ->setParameter('customer', $customers)
            ->orderBy('entity.closedAt', 'ASC')
            ->addOrderBy('entity.id', 'DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        \assert($entity instanceof Order);

        parent::persistEntity($entity);

        $this->setReferer($this->generateUrl('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $entity->getId(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function searchAction(): Response
    {
        $id = \filter_var($this->request->query->get('query'), FILTER_VALIDATE_INT);

        if (false !== $id) {
            $entity = $this->em->getRepository($this->entity['class'])->find($id);
            if (null !== $entity) {
                return $this->redirectToEasyPath($entity, 'show');
            }
        }

        return parent::searchAction();
    }

    private function createPaymentForm(Order $order): FormInterface
    {
        $em = $this->em;

        $customer = $order->getCustomer();
        $balance = null;
        if ($customer instanceof Operand) {
            $balance = $this->paymentManager->balance($customer);
        }

        $forPayment = $order->getTotalForPayment($balance);

        $model = new \stdClass();
        $model->forPayment = $forPayment->isPositive() ? $forPayment : new Money(0, $forPayment->getCurrency());
        $model->recipient = $customer;
        $model->description = '# Начисление по заказу #'.$order->getId();

        $formBuilder = $this->createFormBuilder($model, [
            'label' => false,
            'constraints' => [
                new Assert\Callback(static function (\stdClass $model, ExecutionContextInterface $context): void {
                    /** @var Money|null $money */
                    $money = null;
                    foreach ($model->wallets as ['payment' => $payment]) {
                        $money = null === $money ? $payment : $money->add($payment);
                    }

                    if (!$money->isPositive()) {
                        $context->buildViolation('Сумма должна быть положительной')
                            ->addViolation();
                    }
                }),
            ],
        ])
            ->add('recipient', EasyAdminAutocompleteType::class, [
                'class' => Operand::class,
                'label' => 'Получатель',
                'disabled' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => false,
                'disabled' => true,
            ]);

        $wallets = $em->getRepository(Wallet::class)->findBy(['useInOrder' => true]);

        foreach ($wallets as $index => $wallet) {
            $model->wallets['wallet_'.$wallet->getId()] = [
                'wallet' => $wallet,
                'payment' => 0 === $index
                    ? $model->forPayment
                    : new Money(0, $forPayment->getCurrency()),
            ];

            $walletType = $formBuilder->create('wallet_'.$wallet->getId(), null, [
                'property_path' => 'wallets[wallet_'.$wallet->getId().']',
                'compound' => true,
            ])
                ->add('wallet', TextType::class, [
                    'label' => 'Счет',
                    'disabled' => true,
                ])
                ->add('payment', MoneyType::class, [
                    'constraints' => [
                        new Assert\Callback(static function (Money $money, ExecutionContextInterface $context): void {
                            if ($money->isNegative()) {
                                $context
                                    ->buildViolation('Сумма не может быть отрицательной!')
                                    ->addViolation();
                            }
                        }),
                    ],
                ]);

            $formBuilder->add($walletType);
        }

        return $formBuilder->getForm();
    }

    private function handlePayment(\stdClass $model): void
    {
        $em = $this->em;

        $em->transactional(function () use ($model): void {
            /** @var Wallet $wallet */
            /** @var Money $money */
            foreach ($model->wallets as ['wallet' => $wallet, 'payment' => $money]) {
                if (!$money->isPositive()) {
                    continue;
                }

                if (null !== $model->recipient) {
                    $this->paymentManager->createPayment($model->recipient, $model->description, $money);
                }

                $this->paymentManager->createPayment($wallet, $model->description, $money);
            }
        });
    }

    private function canReceivePayments(): bool
    {
        $registry = $this->container->get(Registry::class);

        $wallets = $registry->repository(Wallet::class)->findBy(['useInOrder' => true]);
        if ([] === $wallets) {
            $this->addFlash('error', 'У Вас нет счетов помеченных как используемые в заказах');
            $this->addFlash('info', 'Для того чтобы принимать платежи создайте счет');

            return false;
        }

        return true;
    }
}
