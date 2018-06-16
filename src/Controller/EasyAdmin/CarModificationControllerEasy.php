<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\CarModification;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarModificationControllerEasy extends AbstractController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->em->getRepository(CarModification::class)->createQueryBuilder('modification')
            ->leftJoin('modification.carGeneration', 'generation')
            ->leftJoin('generation.carModel', 'model')
            ->leftJoin('model.manufacturer', 'manufacturer');

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('modification.name', $key),
                $qb->expr()->like('modification.hp', $key),
                $qb->expr()->like('generation.name', $key),
                $qb->expr()->like('model.name', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        return $qb;
    }

    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (CarModification $modification) {
            return [
                'id' => $modification->getId(),
                'text' => $modification->getDisplayName(),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
