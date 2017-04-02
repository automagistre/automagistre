<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use libphonenumber\PhoneNumberFormat;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonController extends AdminController
{
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $qb = $this->em->getRepository(Person::class)->createQueryBuilder('person');

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

        $data = array_map(function (Person $person) use ($phoneUtils) {
            $formattedTelephone = '';
            if ($tel = $person->getTelephone() ?: $person->getOfficePhone()) {
                $PhoneNumber = $phoneUtils->parse($tel, 'RU');
                $formattedTelephone = $phoneUtils->format($PhoneNumber, PhoneNumberFormat::INTERNATIONAL);
            }

            return [
                'id'   => $person->getId(),
                'text' => sprintf('%s %s', $person->getFullName(), $formattedTelephone),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
