<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Service;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ServiceController extends AdminController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $qb = $this->em->getRepository(Service::class)->createQueryBuilder('service');

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('service.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $string = $query->get('query');
        if ('++' === substr($string, -2)) {
            $service = new Service(rtrim($string, '+'));
            $this->em->persist($service);
            $this->em->flush($service);

            $collection = [$service];
        } else {
            $qb = $this->createSearchQueryBuilder($query->get('entity'), $string, []);
            $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));
            $collection = $paginator->getCurrentPageResults();
        }

        $data = array_map(function (Service $entity) {
            return [
                'id'   => $entity->getId(),
                'text' => sprintf(
                    '%s',
                    $entity->getName()
                ),
            ];
        }, (array) $collection);

        return $this->json(['results' => $data]);
    }
}
