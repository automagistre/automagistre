<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Manufacturer;
use App\Entity\Landlord\Part;
use App\Entity\Landlord\Stockpile;
use App\Entity\Tenant\Motion;
use App\Entity\Tenant\MotionManual;
use App\Entity\Tenant\Order;
use App\Events;
use App\Form\Type\QuantityType;
use App\Manager\DeficitManager;
use App\Manager\PartManager;
use App\Manager\ReservationManager;
use App\Model\Part as PartModel;
use App\Model\WarehousePart;
use App\Partner\Ixora\Finder;
use App\Roles;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use LogicException;
use Money\MoneyFormatter;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormBuilderInterface;
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
            $em = $this->registry->manager(MotionManual::class);
            $quantity = \abs((int) $form->get('quantity')->getData());
            $user = $this->getUser();
            $description = \sprintf('# Ручное пополнение - %s', $user->getId());

            $em->persist(new MotionManual($user, $part, $quantity, $description));
            $em->flush();

            $this->event(Events::PART_ACCRUED, new GenericEvent($part, [
                'quantity' => $quantity,
            ]));

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
            $em = $this->registry->manager(MotionManual::class);
            $quantity = \abs((int) $form->get('quantity')->getData());
            $user = $this->getUser();
            $description = \sprintf('# Ручное списание - %s', $user->getId());

            $em->persist(new MotionManual($user, $part, 0 - $quantity, $description));
            $em->flush();

            $this->event(Events::PART_OUTCOME, new GenericEvent($part, [
                'quantity' => $quantity,
            ]));

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
    protected function createEntityFormBuilder($entity, $view): FormBuilderInterface
    {
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        if ('edit' === $view) {
            $formBuilder->get('number')->setDisabled(!$this->isGranted(Roles::SUPER_ADMIN));
        }

        return $formBuilder;
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

        $qb->leftJoin(
            Stockpile::class,
            'stockpile',
            Join::WITH,
            'stockpile.part = part AND stockpile.tenant = :tenant'
        )
            ->setParameter('tenant', $this->state->tenant())
            ->groupBy('part.id')
            ->orderBy('SUM(stockpile.quantity)', 'DESC');

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

        $normalizer = function (Part $entity, bool $analog = false) {
            $format = '%s - %s (%s) (Склад: %s) | %s';

            if ($analog) {
                $format = ' [АНАЛОГ] '.$format;
            }

            return [
                'id' => $entity->getId(),
                'text' => \sprintf(
                    $format,
                    $entity->getNumber(),
                    $entity->getManufacturer()->getName(),
                    $entity->getName(),
                    $this->partManager->inStock($entity) / 100,
                    $this->formatter->format($entity->getPrice())
                ),
            ];
        };

        $data = [];
        if (3 >= $paginator->getNbResults()) {
            foreach ($paginator->getCurrentPageResults() as $part) {
                /* @var $part Part */
                $data[] = $normalizer($part);

                foreach ($this->partManager->getCrosses($part) as $cross) {
                    if ($cross->getId() === $part->getId()) {
                        continue;
                    }

                    if (0 < $this->partManager->inStock($cross)) {
                        $data[] = $normalizer($cross, true);
                    }
                }
            }
        } else {
            $data = \array_map($normalizer, (array) $paginator->getCurrentPageResults());
        }

        return $this->json(['results' => $data]);
    }

    /**
     * @param Part $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $referer = $this->request->query->get('referer');
        if (null !== $referer) {
            $this->setReferer(\urldecode($referer).'&part_id='.$entity->getId());
        }

        $this->event(Events::PART_CREATED, new GenericEvent($entity));
    }
}
