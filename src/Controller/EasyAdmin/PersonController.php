<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\Person;
use Doctrine\ORM\QueryBuilder;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonController extends AbstractController
{
    /**
     * @param Person $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath($entity, 'show'));
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $em = $this->em;

            $person = $parameters['entity'];

            $parameters['cars'] = $em->getRepository(Car::class)
                ->findBy(['owner' => $person]);
            $parameters['orders'] = $em->getRepository(Order::class)
                ->findBy(['customer' => $person], ['closedAt' => 'DESC'], 20);
            $parameters['payments'] = $em->getRepository(Payment::class)
                ->findBy(['recipient' => $person], ['id' => 'DESC'], 20);

            $amount = $em->createQueryBuilder()
                ->select('SUM(payment.amount)')
                ->from(Payment::class, 'payment')
                ->where('payment.recipient = :recipient')
                ->setParameter('recipient', $person)
                ->getQuery()->getSingleScalarResult();
            $parameters['balance'] = new Money($amount, new Currency('RUB'));
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
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

        $data = array_map(function (Person $person) {
            $formattedTelephone = $this->formatTelephone($person->getTelephone() ?? $person->getOfficePhone());

            return [
                'id' => $person->getId(),
                'text' => sprintf('%s %s', $person->getFullName(), $formattedTelephone),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
