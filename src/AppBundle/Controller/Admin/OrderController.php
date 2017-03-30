<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Order;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Person;
use Doctrine\ORM\Query\Expr\Join;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

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
            ->leftJoin('orders.customer', 'customer')
            ->leftJoin('orders.car', 'car')
            ->leftJoin('car.carModel', 'carModel')
            ->leftJoin('car.carModification', 'carModification')
            ->leftJoin('carModel.manufacturer', 'manufacturer')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class);

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
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('organization.name', $key)
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

        $this->get('event_dispatcher')->addListener(EasyAdminEvents::POST_PERSIST, function (GenericEvent $event) {
            /** @var Order $entity */
            $entity = $event->getArgument('entity');

            $this->request->query->set('referer', $this->generateUrl('easyadmin', [
                'entity' => 'Order',
                'action' => 'show',
                'id'     => $entity->getId(),
            ]));
        });
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
