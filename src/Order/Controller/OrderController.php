<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryOrder;
use App\Calendar\Repository\CalendarEntryRepository;
use App\Car\Entity\Car;
use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Entity\Operand;
use App\Customer\Entity\Organization;
use App\Customer\Entity\Person;
use App\Customer\Enum\CustomerTransactionSource;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Model\OrderTOPart;
use App\Form\Model\OrderTOService;
use App\Form\Type\MoneyType;
use App\Form\Type\OrderTOServiceType;
use App\Manufacturer\Entity\Manufacturer;
use App\MC\Entity\McLine;
use App\MC\Entity\McPart;
use App\Order\Entity\Order;
use App\Order\Entity\OrderId;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Order\Entity\OrderNote;
use App\Order\Enum\OrderStatus;
use App\Order\Event\OrderClosed;
use App\Order\Event\OrderStatusChanged;
use App\Order\Manager\OrderManager;
use App\Part\Entity\PartId;
use App\PartPrice\PartPrice;
use App\Payment\Manager\PaymentManager;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use function array_map;
use function assert;
use function count;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use function explode;
use Generator;
use function in_array;
use function is_numeric;
use function is_string;
use LogicException;
use function mb_strtolower;
use Money\Money;
use Ramsey\Uuid\Uuid;
use function sprintf;
use stdClass;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AbstractController
{
    private PaymentManager $paymentManager;

    private OrderManager $orderManager;

    private PartPrice $partPrice;

    private CalendarEntryRepository $calendarEntryRepository;

    public function __construct(
        PaymentManager $paymentManager,
        OrderManager $orderManager,
        PartPrice $partPrice,
        CalendarEntryRepository $calendarEntryRepository
    ) {
        $this->paymentManager = $paymentManager;
        $this->orderManager = $orderManager;
        $this->partPrice = $partPrice;
        $this->calendarEntryRepository = $calendarEntryRepository;
    }

    public function indexAction(Request $request)
    {
        $id = $request->query->get('id');
        if (is_string($id) && Uuid::isValid($id)) {
            $view = $this->get(Registry::class)->view(OrderId::fromString($id));

            return $this->redirectToEasyPath('Order', 'show', [
                'id' => $view['id'],
            ]);
        }

        return parent::indexAction($request);
    }

    public function TOAction(): Response
    {
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        $carId = $order->getCarId();
        if (null === $carId) {
            throw new LogicException('Car required.');
        }

        /** @var Car $car */
        $car = $this->registry->findBy(Car::class, ['uuid' => $carId]);
        $carModel = $car->vehicleId;
        if (!$carModel instanceof VehicleId) {
            throw new LogicException('CarModel required.');
        }

        if (!$car->equipment->isFilled()) {
            $this->addFlash('warning', 'Для отображения карты ТО необходимо заполнить комплектацию.');

            return $this->redirectToEasyPath($car, 'edit', [
                'order_id' => $order->getId(),
                'validate' => 'equipment',
            ]);
        }

        /** @var Model $carModel */
        $carModel = $this->registry->findBy(Model::class, ['uuid' => $car->vehicleId]);

        $qb = $this->registry->repository(McLine::class)
            ->createQueryBuilder('line')
            ->join('line.equipment', 'equipment')
            ->where('equipment.vehicleId = :model')
            ->andWhere('equipment.equipment.engine.name = :engine')
            ->andWhere('equipment.equipment.engine.capacity = :capacity')
            ->andWhere('equipment.equipment.transmission = :transmission')
            ->andWhere('equipment.equipment.wheelDrive = :wheelDrive')
            ->setParameters([
                'model' => $carModel->toId(),
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
        $periods = array_map('array_shift', $periods);

        if (0 === count($periods)) {
            $this->addFlash('warning', sprintf('Карт ТО для "%s" не найдео.', $this->container->get(IdentifierFormatter::class)->format($car->toId(), 'long')));

            return $this->redirectToReferrer();
        }

        $currentPeriod = $request->query->getInt('period');
        if (!in_array($currentPeriod, $periods, true)) {
            $currentPeriod = $periods[0];
        }

        /** @var OrderTOService[] $services */
        $services = [];

        /** @var McLine[] $lines */
        $lines = $qb->getQuery()->getResult();
        foreach ($lines as $line) {
            if (0 !== $currentPeriod % $line->period) {
                continue;
            }

            $services[$line->getId()] = new OrderTOService(
                $line->work->name,
                $line->work->price,
                (function (array $parts): Generator {
                    foreach ($parts as $part) {
                        assert($part instanceof McPart);

                        yield (int) $part->getId() => new OrderTOPart(
                            $part->partId,
                            $part->quantity,
                            $this->partPrice->sell($part->partId),
                            $part->recommended,
                            !$part->recommended,
                        );
                    }
                })($line->parts->toArray()),
                !$line->recommended,
                $line->recommended
            );
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
            $em = $this->registry->manager(OrderItem::class);
            foreach ($services as $service) {
                if (!$service->selected) {
                    continue;
                }

                $orderItemService = new OrderItemService(
                    $order,
                    $service->service,
                    $service->price,
                );
                $em->persist($orderItemService);

                foreach ($service->parts as $part) {
                    if (!$part->selected) {
                        continue;
                    }

                    $orderItemPart = new OrderItemPart(
                        $order,
                        $part->partId,
                        $part->quantity,
                    );
                    $orderItemPart->setPrice($part->price, $this->partPrice);
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

        $status = OrderStatus::create($this->request->query->getInt('status'));
        if (!$status->isSelectable()) {
            $this->addFlash('error', 'Невозможно вручную установить указанный статус');

            return $this->redirectToReferrer();
        }

        $order->setStatus($status);
        $this->em->flush();

        $this->event(new OrderStatusChanged($order, $status));

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritdoc}
     */
    public function isActionAllowed($actionName): bool
    {
        if ('show' !== $actionName && null !== $id = $this->request->get('id')) {
            $entity = $this->registry->repository(Order::class)->find($id);

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
        $model->description = sprintf('# Аванс по заказу #%s', $order->getId());

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

                $this->handlePayment($model, true);
            });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/payment.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
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

        $customer = null === $order->getCustomerId()
            ? null
            : $this->registry->findBy(Operand::class, ['uuid' => $order->getCustomerId()]);
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
                $this->handlePayment($form->getData(), false);

                goto close;
            }

            return $this->render('easy_admin/order/payment.html.twig', [
                'order' => $order,
                'form' => $form->createView(),
            ]);
        }

        close:

        $this->orderManager->close($order);

        $car = null === $order->getCarId()
            ? null
            : $this->registry->findBy(Car::class, ['uuid' => $order->getCarId()]);
        if ($car instanceof Car) {
            $car->setMileage($order->getMileage());

            $this->registry->manager(Car::class)->flush();
        }

        $this->event(new OrderClosed($order));

        $this->addFlash('success', sprintf('Заказ №%s закрыт', $order->getId()));

        return $this->redirectToEasyPath($order, 'show');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $em = $this->em;
            /** @var Order $entity */
            $entity = $parameters['entity'];

            $parameters['notes'] = $em->getRepository(OrderNote::class)
                ->findBy(['order' => $entity], ['createdAt' => 'DESC']);
            $parameters['car'] = $this->registry->findBy(Car::class, ['uuid' => $entity->getCarId()]);
            $parameters['customer'] = $this->registry->findBy(Operand::class, ['uuid' => $entity->getCustomerId()]);
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
            $entity->setCustomerId($customer->toId());
        }

        $car = $this->getEntity(Car::class);
        if ($car instanceof Car) {
            $entity->setCarId($car->toId());
        }

        $calendarId = $this->getIdentifier(CalendarEntryId::class);
        if ($calendarId instanceof CalendarEntryId) {
            $calendarEntry = $this->calendarEntryRepository->view($calendarId);

            $entity->setCarId($calendarEntry->orderInfo->carId);
            $entity->setCustomerId($calendarEntry->orderInfo->customerId);
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
            $qb->andWhere('entity.customerId = :customer')
                ->setParameter('customer', $customer->toId());
        }

        $car = $this->getEntity(Car::class);
        if ($car instanceof Car) {
            $qb->andWhere('entity.carId = :car')
                ->setParameter('car', $car->toId());
        }

        // EAGER Loading
        $qb
            ->select(['entity', 'items', 'suspends'])
            ->leftJoin('entity.items', 'items')
            ->leftJoin('entity.suspends', 'suspends');

        $partId = $this->getIdentifier(PartId::class);
        if ($partId instanceof PartId) {
            $qb
                ->join(OrderItemPart::class, 'order_item_part', Join::WITH, 'items.id = order_item_part.id AND order_item_part INSTANCE OF '.OrderItemPart::class)
                ->andWhere('order_item_part.partId = :part')
                ->setParameter('part', $partId);
        }

        $request = $this->request;
        if (null === $customer && null === $car && null === $partId && !$request->query->has('all')) {
            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->neq('entity.status', ':closedStatus'),
                    $qb->expr()->eq('DATE(entity.closedAt)', ':today')
                )
            )
                ->setParameter('closedStatus', OrderStatus::closed())
                ->setParameter('today', (new DateTime())->format('Y-m-d'));
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
        $qb = $this->registry->manager(Order::class)
            ->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->leftJoin(Car::class, 'car', Join::WITH, 'o.carId = car.uuid')
            ->leftJoin(Operand::class, 'customer', Join::WITH, 'customer.uuid = o.customerId')
            ->leftJoin(Model::class, 'carModel', Join::WITH, 'carModel.uuid = car.vehicleId')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'manufacturer.id = carModel.manufacturerId')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class);

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(person.firstname)', $key),
                $qb->expr()->like('LOWER(person.lastname)', $key),
                $qb->expr()->like('LOWER(person.telephone)', $key),
                $qb->expr()->like('LOWER(person.email)', $key),
                $qb->expr()->like('LOWER(car.gosnomer)', $key),
                $qb->expr()->like('LOWER(car.identifier)', $key),
                $qb->expr()->like('LOWER(carModel.name)', $key),
                $qb->expr()->like('LOWER(carModel.localizedName)', $key),
                $qb->expr()->like('LOWER(carModel.caseName)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key),
                $qb->expr()->like('LOWER(manufacturer.localizedName)', $key),
                $qb->expr()->like('LOWER(organization.name)', $key)
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
        }

        return $qb
            ->orderBy('o.closedAt', 'ASC')
            ->addOrderBy('o.id', 'DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Order);

        $calendarId = $this->getIdentifier(CalendarEntryId::class);
        if ($calendarId instanceof CalendarEntryId) {
            $this->em->persist(new EntryOrder($calendarId, $entity->toId()));
        }

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
        $id = (string) $this->request->query->get('query');

        if (is_numeric($id)) {
            $entity = $this->em->getRepository(Order::class)->find($id);
            if (null !== $entity) {
                return $this->redirectToEasyPath($entity, 'show');
            }
        }

        return parent::searchAction();
    }

    private function createPaymentForm(Order $order): FormInterface
    {
        $em = $this->em;

        $customer = null === $order->getCustomerId()
            ? null
            : $this->registry->findBy(Operand::class, ['uuid' => $order->getCustomerId()]);
        $balance = null;
        if ($customer instanceof Operand) {
            $balance = $this->paymentManager->balance($customer);
        }

        $forPayment = $order->getTotalForPayment($balance);

        $model = new stdClass();
        $model->forPayment = $forPayment->isPositive() ? $forPayment : new Money(0, $forPayment->getCurrency());
        $model->recipient = $customer;
        $model->description = '# Начисление по заказу #'.$order->getId();

        $formBuilder = $this->createFormBuilder($model, [
            'label' => false,
            'constraints' => [
                new Assert\Callback(static function (stdClass $model, ExecutionContextInterface $context): void {
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
            $walletId = $wallet->toId()->toString();

            $model->wallets['wallet_'.$walletId] = [
                'wallet' => $wallet,
                'payment' => 0 === $index
                    ? $model->forPayment
                    : new Money(0, $forPayment->getCurrency()),
            ];

            $walletType = $formBuilder->create('wallet_'.$walletId, null, [
                'property_path' => 'wallets[wallet_'.$walletId.']',
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

    private function handlePayment(stdClass $model, bool $prepayment): void
    {
        $em = $this->em;

        $em->transactional(function (EntityManagerInterface $em) use ($model, $prepayment): void {
            $order = $this->getEntity(Order::class);
            assert($order instanceof Order);

            /** @var Wallet $wallet */
            /** @var Money $money */
            foreach ($model->wallets as ['wallet' => $wallet, 'payment' => $money]) {
                if (!$money->isPositive()) {
                    continue;
                }

                if (null !== $model->recipient) {
                    $em->persist(
                        new CustomerTransaction(
                            CustomerTransactionId::generate(),
                            $model->recipient->toId(),
                            $money,
                            $prepayment
                                ? CustomerTransactionSource::orderPrepay()
                                : CustomerTransactionSource::orderDebit(),
                            $order->toId()->toUuid(),
                            null
                        )
                    );
                }

                $em->persist(
                    new WalletTransaction(
                        WalletTransactionId::generate(),
                        $wallet->toId(),
                        $money,
                        $prepayment
                            ? WalletTransactionSource::orderPrepay()
                            : WalletTransactionSource::orderDebit(),
                        $order->toId()->toUuid(),
                        null
                    )
                );
            }
        });
    }

    private function canReceivePayments(): bool
    {
        $wallets = $this->registry->repository(Wallet::class)->findBy(['useInOrder' => true]);
        if ([] === $wallets) {
            $this->addFlash('error', 'У Вас нет счетов помеченных как используемые в заказах');
            $this->addFlash('info', 'Для того чтобы принимать платежи создайте счет');

            return false;
        }

        return true;
    }
}
