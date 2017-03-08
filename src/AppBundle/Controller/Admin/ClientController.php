<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Client;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use libphonenumber\PhoneNumberFormat;
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

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key)
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
        $phoneUtils = $this->get('libphonenumber.phone_number_util');

        $data = array_map(function (Client $client) use ($phoneUtils) {
            $person = $client->getPerson();

            $PhoneNumber = $phoneUtils->parse($person->getTelephone(), 'RU');

            return [
                'id' => $client->getId(),
                'text' => sprintf(
                    '%s %s',
                    $person->getFullName(),
                    $phoneUtils->format($PhoneNumber, PhoneNumberFormat::INTERNATIONAL)
                ),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
