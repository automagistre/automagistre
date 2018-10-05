<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Manufacturer;
use App\Entity\Motion;
use App\Entity\MotionManual;
use App\Entity\Order;
use App\Entity\Part;
use App\Form\Type\QuantityType;
use App\Manager\DeficitManager;
use App\Manager\PartManager;
use App\Manager\ReservationManager;
use App\Model\Part as PartModel;
use App\Model\WarehousePart;
use App\Partner\Ixora\Finder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use LogicException;
use Money\MoneyFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AbstractController
{
    /**
     * @var DeficitManager
     */
    private $deficitManager;

    /**
     * @var PartManager
     */
    private $partManager;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var MoneyFormatter
     */
    private $formatter;

    /**
     * @var ReservationManager
     */
    private $reservationManager;

    public function __construct(
        DeficitManager $deficitManager,
        PartManager $partManager,
        Finder $finder,
        MoneyFormatter $formatter,
        ReservationManager $reservationManager
    ) {
        $this->deficitManager = $deficitManager;
        $this->partManager = $partManager;
        $this->finder = $finder;
        $this->formatter = $formatter;
        $this->reservationManager = $reservationManager;
    }

    public function crossAction(): Response
    {
        $left = $this->findCurrentEntity();

        if (!$left instanceof Part) {
            throw new LogicException('Parts required.');
        }

        $form = $this->createFormBuilder()
            ->add('right', EasyAdminAutocompleteType::class, [
                'class' => Part::class,
                'label' => 'Аналог',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotEqualTo(['value' => $left]),
                ],
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->partManager->cross($left, $form->get('right')->getData());

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/cross.html.twig', [
            'part' => $left,
            'form' => $form->createView(),
        ]);
    }

    public function uncrossAction(): Response
    {
        $part = $this->findCurrentEntity();
        if (!$part instanceof Part) {
            throw new LogicException('Parts required.');
        }

        $this->partManager->uncross($part);

        return $this->redirectToReferrer();
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

        return $this->json(\array_map(function (PartModel $model) use ($manufacturerRepository) {
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
        }, \array_filter($parts, function (PartModel $model) use ($number) {
            return false !== \strpos($model->number, $number);
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

        // EAGER Loading
        $qb
            ->addSelect('manufacturer')
            ->join('part.manufacturer', 'manufacturer');

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->getInt('page', 1), 99000);

        $parts = \array_map(function (array $data) {
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
            'parts' => $this->deficitManager->findDeficit(),
        ]);
    }

    public function incomeAction(): Response
    {
        $part = $this->getEntity(Part::class);
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('quantity', QuantityType::class)
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $quantity = (int) $form->get('quantity')->getData();
            $user = $this->getUser();
            $description = \sprintf('# Ручное пополнение - %s', $user->getId());

            $em->persist(new MotionManual($user, $part, \abs($quantity), $description));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/income.html.twig', [
            'part' => $part,
            'form' => $form->createView(),
        ]);
    }

    public function outcomeAction(): Response
    {
        $part = $this->getEntity(Part::class);
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('quantity', QuantityType::class)
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $quantity = (int) $form->get('quantity')->getData();
            $user = $this->getUser();
            $description = \sprintf('# Ручное списание - %s', $user->getId());

            $em->persist(new MotionManual($user, $part, 0 - \abs($quantity), $description));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/outcome.html.twig', [
            'part' => $part,
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $entity = $parameters['entity'];

            $parameters['inStock'] = $this->partManager->inStock($entity);
            $parameters['orders'] = $this->partManager->inOrders($entity);
            $parameters['reservedIn'] = \array_map(function (Order $order) {
                return $order->getId();
            }, $this->reservationManager->orders($entity));
            $parameters['reserved'] = $this->reservationManager->reserved($entity);
            $parameters['crosses'] = $this->partManager->getCrosses($entity);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        // EAGER Loading
        $qb
            ->addSelect('manufacturer')
            ->join('entity.manufacturer', 'manufacturer');

        return $qb;
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

        if (0 === \strpos(\trim($searchQuery), '+')) {
            $qb->andWhere('part.quantity > 0');
            $searchQuery = \ltrim($searchQuery, '+');
        }

        foreach (\explode(' ', \trim($searchQuery)) as $key => $searchString) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('part.name', $key),
                $qb->expr()->like('part.number', $key),
                $qb->expr()->like('manufacturer.name', $key)
            ));

            $qb->setParameter($key, '%'.$searchString.'%');
        }

        $qb->leftJoin(Motion::class, 'motion', Join::WITH, 'motion.part = part')
            ->groupBy('part.id')
            ->orderBy('SUM(motion.quantity)', 'DESC');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $queryString = \str_replace(['.', ',', '-', '_'], '', $query->get('query'));
        $qb = $this->createSearchQueryBuilder($query->get('entity'), $queryString, []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = \array_map(function (Part $entity) {
            return [
                'id' => $entity->getId(),
                'text' => \sprintf(
                    '%s - %s (%s) (Склад: %s) | %s',
                    $entity->getNumber(),
                    $entity->getManufacturer()->getName(),
                    $entity->getName(),
                    $this->partManager->inStock($entity) / 100,
                    $this->formatter->format($entity->getPrice())
                ),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return $this->json(['results' => $data]);
    }
}
