<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\Operand;
use App\Entity\Organization;
use App\Entity\Person;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarController extends AbstractController
{
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
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->em->getRepository(Car::class)->createQueryBuilder('car');

        if ('' === $searchQuery) {
            return $qb;
        }

        $qb->leftJoin('car.carModification', 'modification')
            ->leftJoin('modification.carGeneration', 'generation')
            ->leftJoin('generation.carModel', 'model')
            ->leftJoin('model.manufacturer', 'manufacturer')
            ->leftJoin('car.carModel', 'carModel2')
            ->leftJoin('carModel2.manufacturer', 'manufacturer2')
            ->leftJoin('car.owner', 'owner')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = owner.id AND owner INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = owner.id AND owner INSTANCE OF '.Organization::class);

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('car.year', $key),
                $qb->expr()->like('car.gosnomer', $key),
                $qb->expr()->like('modification.name', $key),
                $qb->expr()->like('generation.name', $key),
                $qb->expr()->like('model.name', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('carModel2.name', $key),
                $qb->expr()->like('manufacturer2.name', $key),
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

        $data = array_map(function (Car $car) use ($ownerId) {
            $carModel = $car->getCarModification() ?: $car->getCarModel();

            $text = $carModel->getDisplayName();

            $gosnomer = $car->getGosnomer();
            if (null !== $gosnomer) {
                $text .= sprintf(' (%s)', $gosnomer);
            }

            $person = $car->getOwner();
            if (null === $ownerId && $person instanceof Person) {
                $text .= ' - '.$person->getFullName();

                $telephone = $person->getTelephone();
                if (null !== $telephone) {
                    $text .= sprintf(' (%s)', $this->formatTelephone($telephone));
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
