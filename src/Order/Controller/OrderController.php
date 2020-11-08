<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Calendar\Entity\CalendarEntryId;
use App\Calendar\Entity\EntryOrder;
use App\Calendar\Repository\CalendarEntryRepository;
use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\CreatedBy\Entity\CreatedBy;
use App\Customer\Entity\CustomerTransactionView;
use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Customer\Entity\Organization;
use App\Customer\Entity\Person;
use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Manufacturer\Entity\Manufacturer;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McEquipmentId;
use App\MC\Entity\McLine;
use App\MC\Entity\McPart;
use App\Note\Entity\NoteView;
use App\Order\Entity\Order;
use App\Order\Entity\OrderClose;
use App\Order\Entity\OrderId;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Order\Entity\OrderItemService;
use App\Order\Enum\OrderStatus;
use App\Order\Event\OrderStatusChanged;
use App\Order\Form\OrderDto;
use App\Order\Form\OrderTOPart;
use App\Order\Form\OrderTOService;
use App\Order\Form\Type\OrderTOServiceType;
use App\Order\Number\NumberGenerator;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Vehicle\Entity\Model;
use App\Wallet\Entity\WalletTransactionView;
use function array_map;
use function assert;
use function count;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use Generator;
use function in_array;
use LogicException;
use function mb_strtolower;
use Pagerfanta\Pagerfanta;
use Ramsey\Uuid\Uuid;
use function sprintf;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use function trim;
use function usort;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AbstractController
{
    private CalendarEntryRepository $calendarEntryRepository;

    private NumberGenerator $numberGenerator;

    public function __construct(CalendarEntryRepository $calendarEntryRepository, NumberGenerator $numberGenerator)
    {
        $this->calendarEntryRepository = $calendarEntryRepository;
        $this->numberGenerator = $numberGenerator;
    }

    public function TOAction(): Response
    {
        $request = $this->request;

        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new LogicException('Order required.');
        }

        $periods = null;
        $currentPeriod = $request->query->getInt('period');

        $carId = $order->getCarId();
        $vehicleId = null;
        $car = null !== $carId ? $this->registry->get(Car::class, $carId) : null;
        $carFilled = $car instanceof Car ? null !== $car->vehicleId && $car->equipment->isFilled() : null;

        /** @var OrderTOService[] $services */
        $services = [];

        $equipmentId = $this->getIdentifier(McEquipmentId::class);

        if ($equipmentId instanceof McEquipmentId || true === $carFilled) {
            $qb = $this->registry->repository(McLine::class)
                ->createQueryBuilder('line')
                ->join('line.equipment', 'equipment');

            if ($equipmentId instanceof McEquipmentId) {
                $qb
                    ->where('equipment.id = :id')
                    ->setParameter('id', $equipmentId);
            } elseif ($car instanceof Car) {
                $qb
                    ->where('equipment.vehicleId = :model')
                    ->andWhere('equipment.equipment.engine.name = :engine')
                    ->andWhere('equipment.equipment.engine.capacity = :capacity')
                    ->andWhere('equipment.equipment.transmission = :transmission')
                    ->andWhere('equipment.equipment.wheelDrive = :wheelDrive')
                    ->setParameters([
                        'model' => $car->vehicleId,
                        'engine' => $car->equipment->engine->name,
                        'capacity' => $car->equipment->engine->capacity,
                        'transmission' => $car->equipment->transmission,
                        'wheelDrive' => $car->equipment->wheelDrive,
                    ]);
            } else {
                throw new LogicException('This must not be reached.');
            }

            $periods = (clone $qb)
                ->select('line.period')
                ->groupBy('line.period')
                ->getQuery()
                ->getArrayResult();
            $periods = array_map('array_shift', $periods);

            if (0 === count($periods)) {
                $this->addFlash('warning', sprintf(
                    'Карт ТО для "%s" не найдео.',
                    $this->display($equipmentId ?? $car->vehicleId, 'long'),
                ));

                return $this->redirectToReferrer();
            }

            if (!in_array($currentPeriod, $periods, true)) {
                $currentPeriod = $periods[0];
            }

            /** @var McLine[] $lines */
            $lines = $qb->getQuery()->getResult();
            foreach ($lines as $line) {
                if (null === $equipmentId) {
                    $equipmentId = $line->equipment->toId();
                }

                if (0 !== $currentPeriod % $line->period) {
                    continue;
                }

                $services[$line->toId()->toString()] = new OrderTOService(
                    $line->work->name,
                    $line->work->price,
                    (function (array $parts): Generator {
                        foreach ($parts as $mcPart) {
                            assert($mcPart instanceof McPart);

                            /** @var PartView $partView */
                            $partView = $this->registry->getBy(PartView::class, ['id' => $mcPart->partId]);

                            yield $mcPart->toId()->toString() => new OrderTOPart(
                                $mcPart->partId,
                                $mcPart->quantity,
                                $partView->sellPrice(),
                                $mcPart->recommended,
                                !$mcPart->recommended,
                            );
                        }
                    })($line->parts->toArray()),
                    !$line->recommended,
                    $line->recommended
                );
            }
        }

        $formBuilder = $this->createFormBuilder(['services' => $services])
            ->add('services', CollectionType::class, [
                'label' => false,
                'entry_type' => OrderTOServiceType::class,
                'allow_add' => false,
                'allow_delete' => false,
            ]);

        $fillForm = null !== $equipmentId && false === $carFilled;
        if ($fillForm) {
            $formBuilder->add('fill', CheckboxType::class, [
                'label' => sprintf(
                    'Заполнить комплектацию "%s" в "%s"?',
                    $this->display($equipmentId),
                    $this->display($carId),
                ),
                'required' => false,
            ]);
        }

        $form = $formBuilder->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager(OrderItem::class);
            foreach ($services as $service) {
                if (!$service->selected) {
                    continue;
                }

                $orderItemService = new OrderItemService(
                    Uuid::uuid6(),
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
                        Uuid::uuid6(),
                        $order,
                        $part->partId,
                        $part->quantity,
                    );
                    $orderItemPart->setPrice(
                        $part->price,
                        $this->registry->get(PartView::class, $part->partId),
                    );
                    $em->persist($orderItemPart);

                    $orderItemPart->setParent($orderItemService);
                }
            }

            if ($fillForm) {
                $equipment = $this->registry->get(McEquipment::class, $equipmentId);

                $car->vehicleId ??= $equipment->vehicleId;
                $car->equipment = $equipment->equipment;
            }

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/to.html.twig', [
            'order' => $order,
            'car' => $car,
            'periods' => $periods,
            'period' => $currentPeriod,
            'currentPeriod' => $currentPeriod,
            'form' => $form->createView(),
            'equipmentForm' => $this->createFormBuilder(['equipment' => $equipmentId])
                ->add('equipment', AutocompleteType::class, [
                    'class' => McEquipment::class,
                    'label' => 'Комплектация',
                    'disabled' => true === $carFilled,
                ])
                ->getForm()
                ->createView(),
            'equipmentId' => $equipmentId,
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

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $em = $this->em;
            /** @var Order $entity */
            $entity = $parameters['entity'];

            $parameters['notes'] = $em->getRepository(NoteView::class)
                ->findBy(['subject' => $entity->toId()->toUuid()], ['id' => 'DESC']);
            $parameters['car'] = $this->registry->findBy(Car::class, ['id' => $entity->getCarId()]);
            $parameters['customer'] = $this->registry->findBy(Operand::class, ['id' => $entity->getCustomerId()]);

            $transactions = [
                ...$em->createQueryBuilder()
                    ->select('t')
                    ->from(CustomerTransactionView::class, 't')
                    ->where('t.sourceId = :sourceId')
                    ->setParameter('sourceId', $entity->toId())
                    ->getQuery()
                    ->getResult(),
                ...$em->createQueryBuilder()
                    ->select('t')
                    ->from(WalletTransactionView::class, 't')
                    ->where('t.sourceId = :sourceId')
                    ->setParameter('sourceId', $entity->toId())
                    ->getQuery()
                    ->getResult(),
            ];
            usort($transactions, fn ($left, $right) => $left->createdAt <=> $right->createdAt);
            $parameters['transactions'] = $transactions;
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): OrderDto
    {
        $dto = new OrderDto();
        $dto->customerId = $this->getIdentifier(OperandId::class);
        $dto->carId = $this->getIdentifier(CarId::class);

        $calendarId = $this->getIdentifier(CalendarEntryId::class);
        if ($calendarId instanceof CalendarEntryId) {
            $calendarEntry = $this->calendarEntryRepository->view($calendarId);

            $dto->carId = $calendarEntry->orderInfo->carId;
            $dto->customerId = $calendarEntry->orderInfo->customerId;
        }

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    protected function findAll(
        $entityClass,
        $page = 1,
        $maxPerPage = 15,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): Pagerfanta {
        if (!$this->request->query->has('all')) {
            $maxPerPage = 200;
        }

        return parent::findAll($entityClass, $page, $maxPerPage, $sortField, $sortDirection, $dqlFilter);
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
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter)
            ->leftJoin(OrderClose::class, 'closed', Join::WITH, 'closed.order = entity')
            ->leftJoin(CreatedBy::class, 'closedBy', Join::WITH, 'closedBy.id = closed.id');

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
                    $qb->expr()->notIn('entity.status', ':closedStatuses'),
                    $qb->expr()->eq('DATE(closedBy.createdAt)', ':today')
                )
            )
                ->setParameter('closedStatuses', [OrderStatus::closed(), OrderStatus::cancelled()])
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
            ->leftJoin(Car::class, 'car', Join::WITH, 'o.carId = car.id')
            ->leftJoin(Operand::class, 'customer', Join::WITH, 'customer.id = o.customerId')
            ->leftJoin(Model::class, 'carModel', Join::WITH, 'carModel.id = car.vehicleId')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'manufacturer.id = carModel.manufacturerId')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class)
            ->leftJoin(CreatedBy::class, 'created', Join::WITH, 'created.id = o.id');

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
            ->orderBy('created.createdAt', 'ASC')
            ->addOrderBy('o.id', 'DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof OrderDto);

        $entity = new Order(
            OrderId::generate(),
            $this->numberGenerator->next(),
        );

        $entity->setCustomerId($dto->customerId);
        $entity->setCarId($dto->carId);
        $entity->setWorker($dto->worker);
        $entity->setMileage($dto->mileage);
        $entity->setDescription($dto->description);

        $calendarId = $this->getIdentifier(CalendarEntryId::class);
        if ($calendarId instanceof CalendarEntryId) {
            $this->em->persist(new EntryOrder($calendarId, $entity->toId()));
        }

        parent::persistEntity($entity);

        $this->setReferer($this->generateUrl('easyadmin', [
            'entity' => 'Order',
            'action' => 'show',
            'id' => $entity->toId()->toString(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function searchAction(): Response
    {
        $number = (string) $this->request->query->get('query');
        $number = trim($number);

        if ('' !== $number) {
            $entity = $this->em->getRepository(Order::class)->findOneBy(['number' => $number]);
            if (null !== $entity) {
                return $this->redirectToEasyPath('Order', 'show', ['id' => $entity->toId()->toString()]);
            }
        }

        return parent::searchAction();
    }
}
