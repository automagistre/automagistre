<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Search;

use Pagerfanta\Pagerfanta;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class Finder
{
    private const MAX_RESULTS = 15;

    private $queryBuilder;

    private $paginator;

    public function __construct(QueryBuilderFactory $queryBuilder, Paginator $paginator)
    {
        $this->queryBuilder = $queryBuilder;
        $this->paginator = $paginator;
    }

    /**
     * @param string $searchQuery
     * @param int    $page
     * @param int    $maxResults
     * @param string $sortField
     * @param string $sortDirection
     *
     * @return Pagerfanta
     */
    public function findByAllProperties(
        array $entityConfig,
        $searchQuery,
        $page = 1,
        $maxResults = self::MAX_RESULTS,
        $sortField = null,
        $sortDirection = null,
    ) {
        $queryBuilder = $this->queryBuilder->createSearchQueryBuilder($entityConfig, $searchQuery, $sortField, $sortDirection);

        return $this->paginator->createOrmPaginator($queryBuilder, $page, $maxResults);
    }
}
