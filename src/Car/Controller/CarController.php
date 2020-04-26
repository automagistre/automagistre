<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Car;
use App\Car\Entity\Note;
use App\Controller\EasyAdmin\AbstractController;
use App\Customer\Domain\Operand;
use App\Customer\Domain\Organization;
use App\Customer\Domain\Person;
use App\Doctrine\Registry;
use App\Entity\Tenant\Order;
use function array_map;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;
use function sprintf;
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
            $entity->owner = $owner;
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
            $parameters['notes'] = $registry->repository(Note::class)
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
            ->leftJoin('car.model', 'model')
            ->leftJoin('model.manufacturer', 'manufacturer')
            ->leftJoin('car.owner', 'owner')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = owner.id AND owner INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = owner.id AND owner INSTANCE OF '.Organization::class);

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
        $em = $this->em;
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query', ''), []);

        $ownerId = $query->get('owner_id');
        if (null !== $ownerId) {
            $qb->andWhere('car.owner = :owner')
                ->setParameter('owner', $em->getReference(Operand::class, $ownerId));
        }

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Car $car) use ($ownerId): array {
            $carModel = $car->model;

            $text = $carModel->getDisplayName();

            $gosnomer = $car->getGosnomer();
            if (null !== $gosnomer) {
                $text .= sprintf(' (%s)', $gosnomer);
            }

            $person = $car->owner;
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
