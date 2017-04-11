<?php

namespace App\Paginator;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class Paginator
{
    const MAX_ITEMS = 15;

    /**
     * @param     $queryBuilder
     * @param int $page
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    public function createOrmPaginator($queryBuilder, $page = 1, $maxPerPage = self::MAX_ITEMS)
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, true));
        $paginator->setMaxPerPage($maxPerPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
