<?php

declare(strict_types=1);

namespace App\Manufacturer\Ports\API;

use App\Manufacturer\Domain\Manufacturer;
use App\Shared\Doctrine\Registry;
use function array_flip;
use function array_key_exists;
use function array_map;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use function is_array;
use function mb_convert_case;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use function sprintf;
use function Symfony\Component\String\u;

final class ManufacturerList
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(ManufacturerListQuery $query): ManufacturerListResponse
    {
        $conn = $this->registry->connection(Manufacturer::class);

        $qb = $conn->createQueryBuilder()
            ->select('t.id AS id')
            ->addSelect('t.name AS name')
            ->addSelect('t.localized_name AS "localizedName"')
            ->from('manufacturer', 't')
            ->addGroupBy('id');

        $stringFields = ['name', 'localized_name'];
        foreach ($query->filtering as $filter) {
            $format = array_key_exists($filter->field, array_flip($stringFields))
                ? 'LOWER(%s)'
                : '%s';

            $format .= ' %s ';

            $type = null;
            if (is_array($filter->value)) {
                $format .= '(:%s)';
                $type = Connection::PARAM_STR_ARRAY;
                $value = array_map(fn (string $value) => mb_convert_case($value, MB_CASE_LOWER), $filter->value);
            } else {
                $format .= ':%s';
                $value = mb_convert_case($filter->value, MB_CASE_LOWER);
            }

            $field = u($filter->field)->snake()->toString();
            $placeholder = $field.'_value';
            $qb
                ->andWhere(sprintf($format, $field, $filter->comparison, $placeholder))
                ->setParameter($placeholder, $value, $type);
        }

        foreach ($query->ordering as $ordering) {
            $field = u($ordering->field)->snake()->toString();
            $qb
                ->addOrderBy($field, $ordering->direction)
                ->addGroupBy($field);
        }

        if ([] === $query->ordering) {
            $qb->orderBy('t.name', 'ASC');
        }

        $pagerfanta = (new Pagerfanta(
            new DoctrineDbalAdapter($qb, static function (QueryBuilder $qb): void {
                $qb->select('COUNT(t.id)')
                    ->resetQueryPart('orderBy')
                    ->resetQueryPart('groupBy');
            })
        ))
            ->setMaxPerPage($query->paging->size)
            ->setCurrentPage($query->paging->page);

        return new ManufacturerListResponse($pagerfanta);
    }
}
