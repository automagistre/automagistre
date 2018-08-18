<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Manufacturer;
use App\Entity\Motion;
use App\Entity\Part;
use App\Manager\DeficitManager;
use App\Model\Part as PartModel;
use App\Model\WarehousePart;
use App\Partner\Ixora\Finder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use LogicException;
use Money\MoneyFormatter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AbstractController
{
    /**
     * @var DeficitManager
     */
    private $partManager;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(
        DeficitManager $partManager,
        EventDispatcherInterface $dispatcher,
        Finder $finder,
        MoneyFormatter $formatter
    ) {
        $this->partManager = $partManager;
        $this->dispatcher = $dispatcher;
        $this->finder = $finder;
        $this->formatter = $formatter;
    }

    public function numberSearchAction(): Response
    {
        if (null === $number = $this->request->query->get('number')) {
            throw new BadRequestHttpException();
        }

        $manufacturerRepository = $this->em->getRepository(Manufacturer::class);

        $parts = $this->finder->search($number);

        if ([] === $parts) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->json(array_map(function (PartModel $model) use ($manufacturerRepository) {
            $manufacturer = $manufacturerRepository->findOneBy(['name' => $model->manufacturer]);
            if (!$manufacturer instanceof Manufacturer) {
                $manufacturer = new Manufacturer();
                $manufacturer->setName($model->manufacturer);
                $this->em->persist($manufacturer);
            }

            return [
                'manufacturer' => [
                    'id' => $manufacturer->getId(),
                    'name' => $manufacturer->getName(),
                ],
                'name' => $model->name,
                'number' => $model->number,
            ];
        }, array_filter($parts, function (PartModel $model) use ($number) {
            return false !== strpos($model->number, $number);
        })));
    }

    public function stockAction(): Response
    {
        $request = $this->request;
        $qb = $this->em->getRepository(Part::class)->createQueryBuilder('part')
            ->addSelect('SUM(motion.quantity) AS quantity')
            ->leftJoin(Motion::class, 'motion', Join::WITH, 'part.id = motion.part')
            ->groupBy('part.id')
            ->having('SUM(motion.quantity) <> 0');

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->getInt('page', 1), 20);

        $parts = array_map(function (array $data) {
            return new WarehousePart([
                'part' => $data[0],
                'quantity' => $data['quantity'],
            ]);
        }, (array) $paginator->getCurrentPageResults());

        return $this->render('easy_admin/part/stock.html.twig', [
            'parts' => $parts,
        ]);
    }

    public function deficitAction(): Response
    {
        return $this->render('easy_admin/part/deficit.html.twig', [
            'parts' => $this->partManager->findDeficit(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function newAction(): Response
    {
        if ($this->request->isXmlHttpRequest() && $this->request->isMethod('POST')) {
            /** @var Part|null $entity */
            $entity = null;
            $this->dispatcher
                ->addListener(EasyAdminEvents::POST_PERSIST, function (GenericEvent $event) use (&$entity): void {
                    $entity = $event->getArgument('entity');
                });

            parent::newAction();

            if (!$entity instanceof Part) {
                throw new LogicException('Part must be returned');
            }

            return $this->json([
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'number' => $entity->getNumber(),
                'manufacturer' => [
                    'id' => $entity->getManufacturer()->getId(),
                    'name' => $entity->getManufacturer()->getName(),
                ],
                'price' => $entity->getPrice(),
            ]);
        }

        return parent::newAction();
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
        $qb = $this->em->getRepository(Part::class)->createQueryBuilder('part')
            ->join('part.manufacturer', 'manufacturer');

        if (0 === strpos(trim($searchQuery), '+')) {
            $qb->andWhere('part.quantity > 0');
            $searchQuery = ltrim($searchQuery, '+');
        }

        foreach (explode(' ', trim($searchQuery)) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('part.name', $key),
                $qb->expr()->like('part.number', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        $qb->orderBy('part.quantity', 'DESC');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $queryString = str_replace(['.', ',', '-', '_'], '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Part $entity) {
            return [
                'id' => $entity->getId(),
                'text' => sprintf(
                    '%s - %s (%s) (Склад: %s) | %s',
                    $entity->getNumber(),
                    $entity->getManufacturer()->getName(),
                    $entity->getName(),
                    $entity->getQuantity() / 100,
                    $this->formatter->format($entity->getPrice())
                ),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
