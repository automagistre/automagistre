<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Order;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AdminController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $qb = $this->em->getRepository(Order::class)->createQueryBuilder('orders')
            ->leftJoin('orders.client', 'client')
            ->leftJoin('client.person', 'person')
            ->leftJoin('orders.car', 'car')
            ->leftJoin('car.carModel', 'carModel')
            ->leftJoin('car.carModification', 'carModification')
            ->leftJoin('carModel.manufacturer', 'manufacturer');

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('car.gosnomer', $key),
                $qb->expr()->like('carModel.name', $key),
                $qb->expr()->like('carModification.name', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        return $qb;
    }

    public function isActionAllowed($actionName): bool
    {
        if ('edit' === $actionName && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(Order::class)->find($id);

            return $entity->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    /**
     * @param Order $entity
     */
    protected function prePersistEntity($entity): void
    {
        $this->preSave($entity);
    }

    /**
     * @param Order $entity
     */
    protected function preUpdateEntity($entity): void
    {
        $this->preSave($entity);
    }

    private function preSave(Order $order): void
    {
        $order->linkOrderToParts();
        $order->linkOrderToServices();
    }
}
