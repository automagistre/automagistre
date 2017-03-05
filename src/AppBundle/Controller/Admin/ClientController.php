<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Client;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ClientController extends AdminController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $qb = $this->em->getRepository(Client::class)->createQueryBuilder('client')
            ->leftJoin('client.person', 'person');

        if (is_numeric($searchQuery)) {
            $qb->where('person.telephone LIKE :search');
        } else {
            $qb->where('person.firstname LIKE :search')
                ->orWhere('person.lastname LIKE :search')
                ->orWhere('person.email LIKE :search');
        }

        $qb->setParameter('search', '%'.$searchQuery.'%')
            ->orderBy('person.lastname', 'ASC')
            ->addOrderBy('person.firstname', 'ASC');

        return $qb;
    }

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Client $client) {
            return [
                'id' => $client->getId(),
                'text' => (string) $client,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
