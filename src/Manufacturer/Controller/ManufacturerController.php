<?php

declare(strict_types=1);

namespace App\Manufacturer\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Form\ManufacturerDto;
use App\Manufacturer\Form\ManufacturerType;
use EasyCorp\Bundle\EasyAdminBundle\Search\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function mb_strtolower;
use function str_replace;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ManufacturerController extends AbstractController
{
    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        $dto = new ManufacturerDto();

        $form = $this->createForm(ManufacturerType::class, $dto)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $id = ManufacturerId::generate();

            $em->persist(
                new Manufacturer(
                    $id,
                    $dto->name,
                    $dto->localizedName,
                ),
            );
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        if (null !== $dto->name && $form->isSubmitted()) {
            /** @var null|Manufacturer $manufacturer */
            $manufacturer = $em->createQueryBuilder()
                ->select('t')
                ->from(Manufacturer::class, 't')
                ->where('LOWER(t.name) = :name')
                ->getQuery()
                ->setParameter('name', mb_strtolower($dto->name))
                ->getOneOrNullResult()
            ;

            if (null !== $manufacturer) {
                return new JsonResponse([
                    'id' => $manufacturer->toId()->toString(),
                    'text' => $this->display($manufacturer->toId()),
                ]);
            }
        }

        return $this->render('easy_admin/widget.html.twig', [
            'id' => 'manufacturer',
            'label' => 'Новый производитель',
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $request = $this->request;

        $queryString = str_replace(['.', ',', '-', '_'], '', (string) $request->query->get('query'));
        $qb = $this->createSearchQueryBuilder((string) $request->query->get('entity'), $queryString, []);

        $paginator = $this->get(Paginator::class)->createOrmPaginator($qb, $request->query->getInt('page', 1));

        return $this->json([
            'results' => array_map(
                fn (Manufacturer $manufacturer) => [
                    'id' => $manufacturer->toId()->toString(),
                    'text' => $this->display($manufacturer->toId()),
                ],
                (array) $paginator->getCurrentPageResults(),
            ),
        ]);
    }
}
