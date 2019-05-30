<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Person;
use App\Event\PersonCreated;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonController extends OperandController
{
    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        \assert($entity instanceof Person);

        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath($entity, 'show'));

        $this->event(new PersonCreated($entity));
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
        $qb = $this->em->getRepository(Person::class)->createQueryBuilder('person');

        foreach (\explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = \array_map(function (Person $person) {
            $formattedTelephone = $this->formatTelephone($person->getTelephone() ?? $person->getOfficePhone());

            return [
                'id' => $person->getId(),
                'text' => \sprintf('%s %s', $person->getFullName(), $formattedTelephone),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
