<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Car;
use App\Entity\Landlord\CarNote;
use App\Entity\Landlord\Operand;
use App\Entity\Landlord\Organization;
use App\Entity\Landlord\Person;
use App\Entity\Tenant\Order;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function getEntityFormOptions($entity, $view): array
    {
        $request = $this->request;
        $options = parent::getEntityFormOptions($entity, $view);

        $options['validation_groups'] = 'equipment' === $request->query->getAlnum('validate')
            ? ['Car', 'CarEquipment', 'CarEngine']
            : ['Car'];

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Car
    {
        $entity = new Car();

        $owner = $this->getEntity(Operand::class);
        if ($owner instanceof Operand) {
            $entity->setOwner($owner);
        }

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

            $registry = $this->container->get(Registry::class);

            $parameters['orders'] = $registry->repository(Order::class)
                ->findBy(['car.id' => $car->getId()], ['closedAt' => 'DESC'], 20);
            $parameters['notes'] = $registry->repository(CarNote::class)
                ->findBy(['car' => $car], ['createdAt' => 'DESC']);
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
        $registry = $this->container->get(Registry::class);
        $qb = $registry->repository(Car::class)->createQueryBuilder('car');

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb
            ->leftJoin('car.carModel', 'carModel')
            ->leftJoin('carModel.manufacturer', 'manufacturer')
            ->leftJoin('car.owner', 'owner')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = owner.id AND owner INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = owner.id AND owner INSTANCE OF '.Organization::class);

        foreach (\explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('car.year', $key),
                $qb->expr()->like('car.gosnomer', $key),
                $qb->expr()->like('car.vin', $key),
                $qb->expr()->like('carModel.name', $key),
                $qb->expr()->like('carModel.localizedName', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('manufacturer.localizedName', $key),
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $em = $this->em;
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query', ''), []);

        $ownerId = $query->get('owner_id');
        if (null !== $ownerId) {
            $qb->andWhere('car.owner = :owner')
                ->setParameter('owner', $em->getReference(Operand::class, $ownerId));
        }

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = \array_map(function (Car $car) use ($ownerId) {
            $carModel = $car->getCarModel();

            $text = $carModel->getDisplayName();

            $gosnomer = $car->getGosnomer();
            if (null !== $gosnomer) {
                $text .= \sprintf(' (%s)', $gosnomer);
            }

            $person = $car->getOwner();
            if (null === $ownerId && $person instanceof Person) {
                $text .= ' - '.$person->getFullName();

                $telephone = $person->getTelephone();
                if (null !== $telephone) {
                    $text .= \sprintf(' (%s)', $this->formatTelephone($telephone));
                }
            }

            return [
                'id' => $car->getId(),
                'text' => $text,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
