<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\CarModel;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarModelController extends AbstractController
{
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
        $qb = $this->em->getRepository(CarModel::class)->createQueryBuilder('model')
            ->leftJoin('model.manufacturer', 'manufacturer');

        foreach (\explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('model.name', $key),
                $qb->expr()->like('model.localizedName', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('manufacturer.localizedName', $key)
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

        $data = \array_map(function (CarModel $entity) {
            return [
                'id' => $entity->getId(),
                'text' => $entity->getDisplayName(),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
