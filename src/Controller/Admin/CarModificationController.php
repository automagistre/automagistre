<?php

namespace App\Controller\Admin;

use App\Entity\CarModification;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarModificationController extends AdminController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
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

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (CarModification $modification) {
            return [
                'id'   => $modification->getId(),
                'text' => $modification->getDisplayName(),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
