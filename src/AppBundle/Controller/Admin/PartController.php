<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Part;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AdminController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $qb = $this->em->getRepository(Part::class)->createQueryBuilder('part')
            ->join('part.manufacturer', 'manufacturer');

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('part.name', $key),
                $qb->expr()->like('part.number', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $queryString = preg_replace('/[^a-zA-Z0-9\s]+/', '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $parts = 0 < $paginator->count()
            ? $paginator->getCurrentPageResults()
            : $this->get('app.part.populator')->search($queryString);

        $data = array_map(function (Part $entity) {
            return [
                'id' => $entity->getId(),
                'text' => sprintf(
                    '%s - %s (%s)',
                    $entity->getNumber(),
                    $entity->getManufacturer()->getName(),
                    $entity->getName()
                ),
            ];
        }, (array) $parts);

        return $this->json(['results' => $data]);
    }
}
