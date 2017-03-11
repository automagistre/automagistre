<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderPart;
use AppBundle\Entity\OrderService;
use Doctrine\Common\Collections\Criteria;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AdminController
{
    /**
     * @param Order $entity
     */
    protected function prePersistEntity($entity): void
    {
        $this->setOrderToPartsAndServices($entity);
    }

    /**
     * @param Order $entity
     */
    protected function preUpdateEntity($entity): void
    {
        $this->setOrderToPartsAndServices($entity);
    }

    private function setOrderToPartsAndServices(Order $order): void
    {
        $criteria = Criteria::create()->where(Criteria::expr()->isNull('order'));

        $order->getServices()->matching($criteria)->map(function (OrderService $service) use ($order) {
            $service->setOrder($order);
        });

        $order->getParts()->matching($criteria)->map(function (OrderPart $part) use ($order) {
            $part->setOrder($order);
        });
    }

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

            return $entity->getStatus()->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }
}
