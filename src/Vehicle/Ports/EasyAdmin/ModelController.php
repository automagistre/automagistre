<?php

declare(strict_types=1);

namespace App\Vehicle\Ports\EasyAdmin;

use App\Controller\EasyAdmin\AbstractController;
use App\Manufacturer\Domain\Manufacturer;
use App\Vehicle\Domain\Model;
use App\Vehicle\Domain\VehicleId;
use App\Vehicle\Infrastructure\Form\ModelDto;
use function array_map;
use function assert;
use Closure;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ModelController extends AbstractController
{
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
        $qb = $this->registry->repository(Model::class)->createQueryBuilder('model')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'model.manufacturerId = manufacturer.uuid');

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(model.name)', $key),
                $qb->expr()->like('LOWER(model.localizedName)', $key),
                $qb->expr()->like('LOWER(model.caseName)', $key),
                $qb->expr()->like('LOWER(manufacturer.name)', $key),
                $qb->expr()->like('LOWER(manufacturer.localizedName)', $key)
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
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

        $data = array_map(fn (Model $entity) => [
            'id' => $entity->getId(),
            'text' => $this->display($entity->toId(), 'long'),
        ], (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): ModelDto
    {
        return $this->createWithoutConstructor(ModelDto::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Model
    {
        $model = $entity;
        assert($model instanceof ModelDto);

        $entity = new Model(
            VehicleId::generate(),
            $model->manufacturer->toId(),
            $model->name,
            $model->localizedName,
            $model->caseName,
            $model->yearFrom,
            $model->yearTill,
        );

        parent::persistEntity($entity);

        return $entity;
    }

    protected function createEditDto(Closure $closure): ?object
    {
        $array = $closure();

        return new ModelDto(
            $array['uuid'],
            $this->registry->findBy(Manufacturer::class, ['uuid' => $array['manufacturerId']]),
            $array['name'],
            $array['localizedName'],
            $array['caseName'],
            $array['yearFrom'],
            $array['yearTill'],
        );
    }

    protected function updateEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof ModelDto);

        /** @var Model $entity */
        $entity = $this->registry->findBy(Model::class, ['uuid' => $dto->vehicleId]);

        $entity->update(
            $dto->name,
            $dto->localizedName,
            $dto->caseName,
            $dto->yearFrom,
            $dto->yearTill,
        );

        parent::updateEntity($entity);
    }
}
