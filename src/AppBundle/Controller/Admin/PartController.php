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
                $qb->expr()->like('part.partname', $key),
                $qb->expr()->like('part.partnumber', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Part $entity) {
            return [
                'id' => $entity->getId(),
                'text' => sprintf(
                    '%s - %s (%s)',
                    $entity->getPartnumber(),
                    $entity->getManufacturer()->getName(),
                    $entity->getPartname()
                ),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
