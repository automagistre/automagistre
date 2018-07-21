<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Person;
use Doctrine\ORM\QueryBuilder;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonController extends AbstractController
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
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

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));
        $phoneUtils = $this->phoneNumberUtil;

        $data = array_map(function (Person $person) use ($phoneUtils) {
            $formattedTelephone = '';
            $tel = $person->getTelephone() ?: $person->getOfficePhone();

            if (null !== $tel) {
                $PhoneNumber = $phoneUtils->parse($tel, 'RU');
                $formattedTelephone = $phoneUtils->format($PhoneNumber, PhoneNumberFormat::INTERNATIONAL);
            }

            return [
                'id' => $person->getId(),
                'text' => sprintf('%s %s', $person->getFullName(), $formattedTelephone),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
