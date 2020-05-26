<?php

declare(strict_types=1);

namespace App\Manufacturer\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use function array_map;
use function str_replace;

final class ManufacturerController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $request = $this->request;
        $isUuid = $request->query->has('use_uuid');

        $queryString = str_replace(['.', ',', '-', '_'], '', $request->query->get('query'));
        $qb = $this->createSearchQueryBuilder($request->query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->get('page', 1));

        return $this->json([
            'results' => array_map(
                fn (Manufacturer $manufacturer) => [
                    'id' => $isUuid ? $manufacturer->toId()->toString() : $manufacturer->getId(),
                    'text' => $this->display($manufacturer->toId()),
                ],
                (array) $paginator->getCurrentPageResults()
            ),
        ]);
    }
}
