<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Car\Form\CarType;
use App\Car\Form\DTO\CarDto;
use App\Car\Repository\CarCustomerRepository;
use App\Customer\Entity\Operand;
use App\Customer\Entity\Organization;
use App\Customer\Entity\Person;
use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use App\Note\Entity\NoteView;
use App\Order\Entity\Order;
use App\Vehicle\Entity\Embedded\Engine;
use App\Vehicle\Entity\Embedded\Equipment;
use App\Vehicle\Entity\Model;
use function array_map;
use function array_merge;
use function assert;
use Closure;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
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
            /** @var Car|null $car */
            $car = $em->createQueryBuilder()
                ->select('t')
                ->from(Car::class, 't')
                ->where('UPPER(t.identifier) = :identifier')
                ->getQuery()
                ->setParameter('identifier', $dto->identifier)
                ->getOneOrNullResult();

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
    protected function createNewEntity(): CarDto
    {
        /** @var CarDto $dto */
        $dto = $this->createWithoutConstructor(CarDto::class);
        $dto->carId = CarId::generate();

        return $dto;
    }

    protected function persistEntity($entity): Car
    {
        $dto = $entity;
        assert($dto instanceof CarDto);

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
                $arr['equipment.engine.capacity']),
            $arr['equipment.transmission'],
            $arr['equipment.wheelDrive'],
        );

        /** @var CarDto $dto */
        $dto = $this->createWithoutConstructor(CarDto::class);
        $dto->carId = $arr['id'];
        $dto->equipment = $equipment;
        $dto->vehicleId = $arr['vehicleId'];
        $dto->identifier = $arr['identifier'];
        $dto->year = $arr['year'];
        $dto->caseType = $arr['caseType'];
        $dto->description = $arr['description'];
        $dto->gosnomer = $arr['gosnomer'];

        return $dto;
    }

    protected function updateEntity($entity): Car
    {
        $dto = $entity;
        assert($dto instanceof CarDto);

        $entity = $this->registry->findBy(Car::class, ['id' => $dto->carId]);

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
                ->findBy(['carId' => $car->toId()], ['closedAt' => 'DESC'], 20);
            $parameters['notes'] = $this->registry->repository(NoteView::class)
                ->findBy(['subject' => $car->toId()->toUuid()], ['id' => 'DESC']);
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
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->registry->repository(Car::class)->createQueryBuilder('car')
            ->leftJoin(Order::class, 'o', Join::WITH, 'o.carId = car.id');

        $customerId = $this->request->query->get('customer_id');
        if (null !== $customerId) {
            $qb->andWhere('o.customerId = :customer')
                ->setParameter('customer', $customerId);
        }

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin(Model::class, 'model', Join::WITH, 'model.id = car.vehicleId')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'manufacturer.id = model.manufacturerId')
            ->leftJoin(Operand::class, 'customer', Join::WITH, 'o.customerId = customer.id')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class);

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(CAST(car.year AS string))', $key),
                $qb->expr()->like('LOWER(car.gosnomer)', $key),
                $qb->expr()->like('LOWER(car.identifier)', $key),
                $qb->expr()->like('LOWER(car.description)', $key),
                $qb->expr()->like('LOWER(model.name)', $key),
                $qb->expr()->like('LOWER(model.localizedName)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key),
                $qb->expr()->like('LOWER(manufacturer.localizedName)', $key),
                $qb->expr()->like('LOWER(person.firstname)', $key),
                $qb->expr()->like('LOWER(person.lastname)', $key),
                $qb->expr()->like('LOWER(person.telephone)', $key),
                $qb->expr()->like('LOWER(person.email)', $key),
                $qb->expr()->like('LOWER(organization.name)', $key)
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

        $qb = $this->createSearchQueryBuilder((string) $query->get('entity'), $query->get('query', ''), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->getInt('page', 1));

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
