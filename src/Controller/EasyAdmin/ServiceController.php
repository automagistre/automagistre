<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Service;
use Doctrine\ORM\QueryBuilder;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ServiceController extends AbstractController
{
    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
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
        $qb = $this->em->getRepository(Service::class)->createQueryBuilder('service');

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('service.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $string = $query->get('query');
        if ('++' === substr($string, -2)) {
            $pieces = explode(' ', trim(rtrim($string, '+')));
            $price = is_numeric(end($pieces)) ? array_pop($pieces) : 0;

            $service = new Service(implode(' ', $pieces), new Money($price * 100, new Currency('RUB')));
            $this->em->persist($service);
            $this->em->flush($service);

            $collection = [$service];
        } else {
            $qb = $this->createSearchQueryBuilder($query->get('entity'), $string, []);
            $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));
            $collection = $paginator->getCurrentPageResults();
        }

        $data = array_map(function (Service $entity) {
            return [
                'id' => $entity->getId(),
                'text' => sprintf('%s (%s)', $entity->getName(), $this->moneyFormatter->format($entity->getPrice())),
                'price' => $entity->getPrice()->getAmount() / 100,
            ];
        }, (array) $collection);

        return $this->json(['results' => $data]);
    }
}
