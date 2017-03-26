<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Part;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AdminController
{
    protected function newAction()
    {
        if ($this->request->isXmlHttpRequest() && $this->request->isMethod('POST')) {
            /** @var Part $entity */
            $entity = null;
            $this->get('event_dispatcher')
                ->addListener(EasyAdminEvents::POST_PERSIST, function (GenericEvent $event) use (&$entity) {
                    $entity = $event->getArgument('entity');
                });

            parent::newAction();

            return $this->json([
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'number' => $entity->getNumber(),
                'manufacturer' => [
                    'id' => $entity->getManufacturer()->getId(),
                    'name' => $entity->getManufacturer()->getName(),
                ],
                'price' => $entity->getPrice(),
            ]);
        }

        return parent::newAction();
    }

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

        $queryString = str_replace(['.', ',', '-', '_'], '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

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
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
