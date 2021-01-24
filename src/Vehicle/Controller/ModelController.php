<?php

declare(strict_types=1);

namespace App\Vehicle\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use App\Vehicle\Form\ModelCreate;
use App\Vehicle\Form\ModelUpdate;
use App\Vehicle\Form\VehicleType;
use Closure;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function assert;
use function explode;
use function mb_strtolower;
use function mb_strtoupper;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ModelController extends AbstractController
{
    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        $dto = new ModelCreate();

        $form = $this->createForm(VehicleType::class, $dto)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $id = VehicleId::generate();

            $em->persist(
                new Model(
                    $id,
                    $dto->manufacturerId,
                    $dto->name,
                    $dto->localizedName,
                    $dto->caseName,
                    $dto->yearFrom,
                    $dto->yearTill,
                ),
            );
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        if (null !== $dto->manufacturerId && null !== $dto->name && null !== $dto->caseName && $form->isSubmitted()) {
            /** @var null|Model $vehicle */
            $vehicle = $em->createQueryBuilder()
                ->select('t')
                ->from(Model::class, 't')
                ->where('t.manufacturerId = :manufacturerId')
                ->andWhere('LOWER(t.name) = :name')
                ->andWhere('UPPER(t.caseName) = :caseName')
                ->getQuery()
                ->setParameter('manufacturerId', $dto->manufacturerId)
                ->setParameter('name', mb_strtolower($dto->name))
                ->setParameter('caseName', mb_strtoupper($dto->caseName))
                ->getOneOrNullResult()
            ;

            if (null !== $vehicle) {
                return new JsonResponse([
                    'id' => $vehicle->toId()->toString(),
                    'text' => $this->display($vehicle->toId()),
                ]);
            }
        }

        return $this->render('easy_admin/widget.html.twig', [
            'id' => 'vehicle',
            'label' => 'Новый кузов',
            'form' => $form->createView(),
        ]);
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
        $qb = $this->registry->repository(Model::class)->createQueryBuilder('model')
            ->leftJoin(Manufacturer::class, 'manufacturer', Join::WITH, 'model.manufacturerId = manufacturer.id')
        ;

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

        $qb = $this->createSearchQueryBuilder((string) $query->get('entity'), (string) $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->getInt('page', 1));

        $data = array_map(fn (Model $entity) => [
            'id' => $entity->toId()->toString(),
            'text' => $this->display($entity->toId(), 'long'),
        ], (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): ModelCreate
    {
        return new ModelCreate();
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Model
    {
        $dto = $entity;
        assert($dto instanceof ModelCreate);

        $entity = new Model(
            VehicleId::generate(),
            $dto->manufacturerId,
            $dto->name,
            $dto->localizedName,
            $dto->caseName,
            $dto->yearFrom,
            $dto->yearTill,
        );

        parent::persistEntity($entity);

        return $entity;
    }

    protected function createEditDto(Closure $closure): ModelUpdate
    {
        $array = $closure();

        $dto = new ModelUpdate();
        $dto->vehicleId = $array['id'];
        $dto->manufacturerId = $array['manufacturerId'];
        $dto->name = $array['name'];
        $dto->localizedName = $array['localizedName'];
        $dto->caseName = $array['caseName'];
        $dto->yearFrom = $array['yearFrom'];
        $dto->yearTill = $array['yearTill'];

        return $dto;
    }

    protected function updateEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof ModelUpdate);

        /** @var Model $entity */
        $entity = $this->registry->findBy(Model::class, ['id' => $dto->vehicleId]);

        $entity->name = $dto->name;
        $entity->localizedName = $dto->localizedName;
        $entity->caseName = $dto->caseName;
        $entity->yearFrom = $dto->yearFrom;
        $entity->yearTill = $dto->yearTill;

        parent::updateEntity($entity);
    }
}
