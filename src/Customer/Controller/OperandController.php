<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Car\Repository\CarCustomerRepository;
use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandNote;
use App\Customer\Entity\Organization;
use App\Customer\Entity\Person;
use App\EasyAdmin\Controller\AbstractController;
use App\Entity\Tenant\OperandTransaction;
use App\Order\Entity\Order;
use App\Payment\Manager\PaymentManager;
use function array_map;
use function array_merge;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;
use function sprintf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class OperandController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            PaymentManager::class,
            CarCustomerRepository::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction(Request $request): Response
    {
        if (self::class !== static::class || 'autocomplete' === $request->query->get('action')) {
            return parent::indexAction($request);
        }

        $this->initialize($request);
        $id = $request->query->get('id');

        $entity = $this->registry->repository(Operand::class)->find($id);
        $config = $this->get('easyadmin.config.manager')->getEntityConfigByClass($this->registry->class($entity));

        return $this->redirectToRoute('easyadmin', array_merge($request->query->all(), [
            'entity' => $config['name'],
        ]));
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
        $isBalanceSort = 'balance' === $sortField;

        $qb = parent::createListQueryBuilder(
            $entityClass,
            $sortDirection,
            $isBalanceSort ? null : $sortField,
            $dqlFilter
        );

        if ($isBalanceSort) {
            $this->sortByBalance($qb, $sortDirection);
        }

        return $qb;
    }

    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $isBalanceSort = 'balance' === $sortField;

        $qb = parent::createSearchQueryBuilder(
            $entityClass,
            $searchQuery,
            $searchableFields,
            $isBalanceSort ? null : $sortField,
            $sortDirection,
            $dqlFilter,
        );

        if ($isBalanceSort) {
            $this->sortByBalance($qb, $sortDirection);
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            /** @var Operand $operand */
            $operand = $parameters['entity'];
            /** @var CarCustomerRepository $carRepository */
            $carRepository = $this->container->get(CarCustomerRepository::class);

            $parameters['cars'] = $carRepository->carsByCustomer($operand->toId());
            $parameters['orders'] = $this->registry->repository(Order::class)
                ->findBy(['customerId' => $operand->toId()], ['closedAt' => 'DESC'], 20);
            $parameters['payments'] = $this->registry->repository(OperandTransaction::class)
                ->findBy(['recipient.id' => $operand->getId()], ['id' => 'DESC'], 20);
            $parameters['notes'] = $this->registry->repository(OperandNote::class)
                ->findBy(['operand' => $operand], ['createdAt' => 'DESC']);
            $parameters['balance'] = $this->get(PaymentManager::class)->balance($operand);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;
        $isUuid = $query->has('use_uuid');

        $qb = $this->registry->repository(Operand::class)->createQueryBuilder('operand')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = operand.id AND operand INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = operand.id AND operand INSTANCE OF '.Organization::class);

        $carId = $query->get('car_id');
        if (null !== $carId) {
            $qb
                ->leftJoin(Order::class, 'o', Join::WITH, 'o.customerId = operand.uuid')
                ->orderBy('o.closedAt', 'DESC')
                ->andWhere('o.carId = :car')
                ->setParameter('car', $carId);
        }

        $search = $query->has('query') ? explode(' ', $query->get('query')) : [];
        foreach ($search as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(person.firstname)', $key),
                $qb->expr()->like('LOWER(person.lastname)', $key),
                $qb->expr()->like('LOWER(person.telephone)', $key),
                $qb->expr()->like('LOWER(person.email)', $key),
                $qb->expr()->like('LOWER(organization.name)', $key)
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
        }

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Operand $entity) use ($isUuid): array {
            $text = $entity->getFullName();

            $telephone = $entity->getTelephone();
            if (null !== $telephone) {
                $text .= sprintf(' (%s)', $this->formatTelephone($telephone));
            }

            return [
                'id' => $isUuid ? $entity->toId()->toString() : $entity->getId(),
                'text' => $text,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }

    private function sortByBalance(QueryBuilder $qb, ?string $sortDirection): void
    {
        $qb
            ->addSelect('SUM(COALESCE(ot.amount.amount,0)) AS HIDDEN balance')
            ->leftJoin(
                OperandTransaction::class,
                'ot',
                Join::WITH,
                'ot.recipient.id = entity.id')
            ->orderBy('balance', $sortDirection)
            ->groupBy('entity');
    }
}
