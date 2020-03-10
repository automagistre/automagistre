<?php

declare(strict_types=1);

namespace App\Car\Controller;

use App\Car\Entity\Model;
use App\Controller\EasyAdmin\AbstractController;
use App\Doctrine\Registry;
use function array_map;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function strtolower;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ModelController extends AbstractController
{
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

        // EAGER Loading
        $qb
            ->addSelect('manufacturer')
            ->join('entity.manufacturer', 'manufacturer');

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
        $registry = $this->container->get(Registry::class);

        $qb = $registry->repository(Model::class)->createQueryBuilder('model')
            ->leftJoin('model.manufacturer', 'manufacturer');

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(model.name)', $key),
                $qb->expr()->like('LOWER(model.localizedName)', $key),
                $qb->expr()->like('LOWER(model.caseName)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key),
                $qb->expr()->like('LOWER(manufacturer.localizedName)', $key)
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
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

        $data = array_map(fn (Model $entity) => [
            'id' => $entity->getId(),
            'text' => $entity->getDisplayName(),
        ], (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
