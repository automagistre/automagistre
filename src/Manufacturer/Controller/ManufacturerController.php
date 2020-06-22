<?php

declare(strict_types=1);

namespace App\Manufacturer\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use function array_map;
use function str_replace;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ManufacturerController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $request = $this->request;
        $isUuid = $request->query->has('use_uuid');

        $queryString = str_replace(['.', ',', '-', '_'], '', (string) $request->query->get('query'));
        $qb = $this->createSearchQueryBuilder((string) $request->query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->getInt('page', 1));

        return $this->json([
            'results' => array_map(
                fn (Manufacturer $manufacturer) => [
                    'id' => $manufacturer->toId()->toString(),
                    'text' => $this->display($manufacturer->toId()),
                ],
                (array) $paginator->getCurrentPageResults()
            ),
        ]);
    }
}
