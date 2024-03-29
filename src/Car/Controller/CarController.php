<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Car\Form\CarType;
use App\Car\Form\DTO\CarCreate;
use App\Car\Form\DTO\CarUpdate;
use App\Car\Repository\CarCustomerRepository;
use App\Customer\Entity\CustomerView;
use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use App\Note\Entity\NoteView;
use App\Order\Entity\Order;
use App\Vehicle\Entity\Embedded\Engine;
use App\Vehicle\Entity\Embedded\Equipment;
use App\Vehicle\Entity\Model;
use Closure;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Search\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function array_merge;
use function assert;
use function explode;
use function mb_strtolower;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CarController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CarCustomerRepository::class,
        ]);
    }

    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        $dto = $this->createNewEntity();

        $form = $this->createForm(CarType::class, $dto, [
            'action' => $this->generateEasyPath('Car', 'widget'),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = CarId::generate();

            $entity = new Car(
                $id,
            );
            $entity->equipment = $dto->equipment;
            $entity->setGosnomer($dto->gosnomer);
            $entity->identifier = $dto->identifier;
            $entity->year = $dto->year;
            $entity->caseType = $dto->caseType;
            $entity->description = $dto->description;
            $entity->vehicleId = $dto->vehicleId;

            $em->persist($entity);
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        if ('' !== $dto->identifier && $form->isSubmitted()) {
            /** @var null|Car $car */
            $car = $em->createQueryBuilder()
                ->select('t')
                ->from(Car::class, 't')
                ->where('UPPER(t.identifier) = :identifier')
                ->getQuery()
                ->setParameter('identifier', $dto->identifier)
                ->getOneOrNullResult()
            ;

            if (null !== $car) {
                return new JsonResponse([
                    'id' => $car->toId()->toString(),
                    'text' => $this->display($car->toId()),
                ]);
            }
        }

        return $this->render('easy_admin/car/widget.html.twig', [
            'id' => 'car',
            'label' => 'Новый автомобиль',
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): CarCreate
    {
        return new CarCreate();
    }

    protected function persistEntity($entity): Car
    {
        $dto = $entity;
        assert($dto instanceof CarCreate);

        $entity = new Car(
            CarId::generate(),
        );
        $entity->equipment = $dto->equipment;
        $entity->setGosnomer($dto->gosnomer);
        $entity->identifier = $dto->identifier;
        $entity->year = $dto->year;
        $entity->caseType = $dto->caseType;
        $entity->description = $dto->description;
        $entity->vehicleId = $dto->vehicleId;

        parent::persistEntity($entity);

        return $entity;
    }

    protected function createEditDto(Closure $callable): ?object
    {
        $arr = $callable();

        $equipment = new Equipment(
            new Engine(
                $arr['equipment.engine.name'],
                $arr['equipment.engine.type'],
                $arr['equipment.engine.airIntake'],
                $arr['equipment.engine.injection'],
                $arr['equipment.engine.capacity'],
            ),
            $arr['equipment.transmission'],
            $arr['equipment.wheelDrive'],
        );

        return new CarUpdate(
            $arr['id'],
            $arr['vehicleId'],
            $equipment,
            $arr['identifier'],
            $arr['year'],
            $arr['caseType'],
            $arr['description'],
            $arr['gosnomer'],
        );
    }

    protected function updateEntity($entity): Car
    {
        $dto = $entity;
        assert($dto instanceof CarUpdate);

        $entity = $this->registry->getBy(Car::class, ['id' => $dto->carId]);

        $entity->equipment = $dto->equipment;
        $entity->setGosnomer($dto->gosnomer);
        $entity->identifier = $dto->identifier;
        $entity->year = $dto->year;
        $entity->caseType = $dto->caseType;
        $entity->description = $dto->description;
        $entity->vehicleId = $dto->vehicleId;

        parent::updateEntity($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            /** @var Car $car */
            $car = $parameters['entity'];

            /** @var CarCustomerRepository $customers */
            $customers = $this->container->get(CarCustomerRepository::class);

            $parameters['orders'] = $this->registry->repository(Order::class)
                ->findBy(['carId' => $car->toId()], ['id' => 'DESC'], 20)
            ;
            $parameters['notes'] = $this->registry->repository(NoteView::class)
                ->findBy(['subject' => $car->toId()->toUuid()], ['id' => 'DESC'])
            ;
            $parameters['customers'] = $customers->customersByCar($car->toId());
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
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
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = $this->registry->repository(Car::class)->createQueryBuilder('car')
            ->leftJoin(Order::class, 'o', Join::WITH, 'o.carId = car.id')
        ;

        $customerId = $this->request->query->get('customer_id');

        if (null !== $customerId) {
            $qb->andWhere('o.customerId = :customer')
                ->setParameter('customer', $customerId)
            ;
        }

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin(Model::class, 'model', Join::WITH, 'model.id = car.vehicleId')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'manufacturer.id = model.manufacturerId')
            ->leftJoin(CustomerView::class, 'customer', Join::WITH, 'customer.id = o.customerId')
        ;

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(CAST(car.year AS string))', $key),
                $qb->expr()->like('LOWER(car.gosnomer)', $key),
                $qb->expr()->like('LOWER(car.identifier)', $key),
                $qb->expr()->like('LOWER(car.description)', $key),
                $qb->expr()->like('LOWER(model.name)', $key),
                $qb->expr()->like('LOWER(model.localizedName)', $key),
                $qb->expr()->like('LOWER(model.caseName)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key),
                $qb->expr()->like('LOWER(manufacturer.localizedName)', $key),
                $qb->expr()->like('LOWER(customer.fullName)', $key),
                $qb->expr()->like('LOWER(customer.telephone)', $key),
                $qb->expr()->like('LOWER(customer.email)', $key),
            ));

            $qb->setParameter($key, '%'.mb_strtolower($searchString).'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder((string) $query->get('entity'), (string) $query->get('query'), []);

        $paginator = $this->container->get(Paginator::class)->createOrmPaginator($qb, $query->getInt('page', 1));

        $data = array_map(function (Car $car): array {
            $text = $this->display($car->toId(), 'autocomplete');

            return [
                'id' => $car->toId()->toString(),
                'text' => $text,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
