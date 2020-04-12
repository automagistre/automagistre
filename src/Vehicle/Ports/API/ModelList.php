<?php

declare(strict_types=1);

namespace App\Vehicle\Ports\API;

use App\Car\Entity\Model;
use App\Doctrine\Registry;
use Doctrine\DBAL\Query\QueryBuilder;
use function implode;
use function is_array;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use function sprintf;
use function Symfony\Component\String\u;

final class ModelList
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(ModelListQuery $query): ModelListResponse
    {
        $em = $this->registry->manager(Model::class);

        $qb = $em->getConnection()->createQueryBuilder()
            ->select('t.id AS id')
            ->addSelect('t.manufacturer_id AS "manufacturerId"')
            ->addSelect('t.name AS name')
            ->addSelect('t.localized_name AS "localizedName"')
            ->addSelect('t.case_name AS model')
            ->addSelect('t.year_from AS "yearFrom"')
            ->addSelect('t.year_till AS "yearTill"')
            ->addSelect('\'\' AS url')
            ->addSelect('\'\' AS img')
            ->from('car_model', 't');

        foreach ($query->filtering as $filter) {
            [$format, $value] = is_array($filter->value)
                ? ['t.%s %s (:%s)', implode(',', $filter->value)]
                : ['t.%s %s :%s', $filter->value];

            $field = u($filter->field)->snake()->toString();
            $qb
                ->andWhere(sprintf($format, $field, $filter->comparison, $field))
                ->setParameter($field, $value);
        }

        foreach ($query->ordering as $orderBy) {
            $qb->addOrderBy($orderBy->field, $orderBy->direction);
        }

        $pagerfanta = (new Pagerfanta(
            new DoctrineDbalAdapter($qb, fn (QueryBuilder $qb) => $qb->select('COUNT(t.id)'))
        ))
            ->setMaxPerPage($query->paging->size)
            ->setCurrentPage($query->paging->page);

        return new ModelListResponse($pagerfanta);
    }
}
