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
        return $this->em->getRepository(Order::class)->createQueryBuilder('orders')
            ->leftJoin('orders.client', 'client')
            ->leftJoin('client.person', 'person')
            ->leftJoin('orders.car', 'car')
            ->leftJoin('car.carModel', 'carModel')
            ->leftJoin('car.carModification', 'carModification')
            ->leftJoin('carModel.manufacturer', 'manufacturer')
            ->where('person.firstname LIKE :search')
            ->orWhere('person.lastname LIKE :search')
            ->orWhere('car.gosnomer LIKE :search')
            ->orWhere('carModel.name LIKE :search')
            ->orWhere('carModification.name LIKE :search')
            ->orWhere('manufacturer.name LIKE :search')
            ->setParameter('search', '%'.$searchQuery.'%');
    }

    public function isActionAllowed($actionName): bool
    {
        if ('edit' === $actionName && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(Order::class)->find($id);

            return $entity->getStatus()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }
}
