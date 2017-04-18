<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Manufacturer;
use App\Entity\Motion;
use App\Entity\Part;
use App\Manager\PartManager;
use App\Model\Part as PartModel;
use App\Model\WarehousePart;
use App\Part\Finder;
use Doctrine\ORM\Query\Expr\Join;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AdminController
{
    /**
     * @var PartManager
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

    public function __construct(PartManager $partManager, EventDispatcherInterface $dispatcher, Finder $finder)
    {
        $this->partManager = $partManager;
        $this->dispatcher = $dispatcher;
        $this->finder = $finder;
    }

    protected function newAction()
    {
        if ($this->request->isXmlHttpRequest() && $this->request->isMethod('POST')) {
            /** @var Part $entity */
            $entity = null;
            $this->dispatcher
                ->addListener(EasyAdminEvents::POST_PERSIST, function (GenericEvent $event) use (&$entity) {
                    $entity = $event->getArgument('entity');
                });

            parent::newAction();

            return $this->json([
                'id'           => $entity->getId(),
                'name'         => $entity->getName(),
                'number'       => $entity->getNumber(),
                'manufacturer' => [
                    'id'   => $entity->getManufacturer()->getId(),
                    'name' => $entity->getManufacturer()->getName(),
                ],
                'price'        => $entity->getPrice(),
            ]);
        }

        return parent::newAction();
    }

    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $qb = $this->em->getRepository(Part::class)->createQueryBuilder('part')
            ->join('part.manufacturer', 'manufacturer');

        foreach (explode(' ', $searchQuery) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('part.name', $key),
                $qb->expr()->like('part.number', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        return $qb;
    }

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $queryString = str_replace(['.', ',', '-', '_'], '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Part $entity) {
            return [
                'id'   => $entity->getId(),
                'text' => sprintf(
                    '%s - %s (%s)',
                    $entity->getNumber(),
                    $entity->getManufacturer()->getName(),
                    $entity->getName()
                ),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }

    public function numberSearchAction()
    {
        if (!$number = $this->request->query->get('number')) {
            throw new BadRequestHttpException();
        }

        $manufacturerRepository = $this->em->getRepository(Manufacturer::class);

        $parts = $this->finder->search($number);

        if (!$parts) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->json(array_map(function (PartModel $model) use ($manufacturerRepository) {
            $manufacturer = $manufacturerRepository->findOneBy(['name' => $model->manufacturer]);
            if (!$manufacturer) {
                $manufacturer = new Manufacturer();
                $manufacturer->setName($model->manufacturer);
                $this->em->persist($manufacturer);
            }

            return [
                'manufacturer' => [
                    'id'   => $manufacturer->getId(),
                    'name' => $manufacturer->getName(),
                ],
                'name'         => $model->name,
                'number'       => $model->number,
            ];
        }, array_filter($parts, function (PartModel $model) use ($number) {
            return false !== strpos($model->number, $number);
        })));
    }

    public function stockAction()
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
                'part'     => $data[0],
                'quantity' => $data['quantity'],
            ]);
        }, (array) $paginator->getCurrentPageResults());

        return $this->render('easy_admin/part/stock.html.twig', [
            'parts' => $parts,
        ]);
    }

    public function deficitAction()
    {
        return $this->render('easy_admin/part/deficit.html.twig', [
            'parts' => $this->partManager->findDeficit(),
        ]);
    }
}
