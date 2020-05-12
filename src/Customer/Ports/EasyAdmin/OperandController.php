<?php

declare(strict_types=1);

namespace App\Customer\Ports\EasyAdmin;

use App\Car\Entity\Car;
use App\Controller\EasyAdmin\AbstractController;
use App\Customer\Domain\Operand;
use App\Customer\Domain\OperandNote;
use App\Customer\Domain\Organization;
use App\Customer\Domain\Person;
use App\Doctrine\Registry;
use App\Entity\Landlord\Balance;
use App\Entity\Tenant\OperandTransaction;
use App\Manager\PaymentManager;
use App\Order\Entity\Order;
use App\State;
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

        $id = $request->query->get('id');
        $registry = $this->container->get(Registry::class);

        $entity = $registry->repository(Operand::class)->find($id);
        $config = $this->get('easyadmin.config.manager')->getEntityConfigByClass($registry->class($entity));

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
            $state = $this->container->get(State::class);

            $qb
                ->addSelect('SUM(COALESCE(b.price.amount,0)) AS HIDDEN balance')
                ->leftJoin(
                    Balance::class,
                    'b',
                    Join::WITH,
                    'b.operand = entity.id AND b.tenant = :tenant')
                ->orderBy('balance', $sortDirection)
                ->groupBy('entity.id')
                ->setParameter('tenant', $state->tenant());
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $registry = $this->container->get(Registry::class);

            /** @var Operand $operand */
            $operand = $parameters['entity'];

            $parameters['cars'] = $registry->viewListBy(Car::class, ['owner' => $operand]);
            $parameters['orders'] = $registry->repository(Order::class)
                ->findBy(['customer.id' => $operand->getId()], ['closedAt' => 'DESC'], 20);
            $parameters['payments'] = $registry->repository(OperandTransaction::class)
                ->findBy(['recipient.id' => $operand->getId()], ['id' => 'DESC'], 20);
            $parameters['notes'] = $registry->repository(OperandNote::class)
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

        $registry = $this->container->get(Registry::class);

        $qb = $registry->repository(Operand::class)->createQueryBuilder('operand')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = operand.id AND operand INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = operand.id AND operand INSTANCE OF '.Organization::class);

        foreach (explode(' ', $query->get('query')) as $key => $item) {
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

        $data = array_map(function (Operand $entity): array {
            $text = $entity->getFullName();

            $telephone = $entity->getTelephone();
            if (null !== $telephone) {
                $text .= sprintf(' (%s)', $this->formatTelephone($telephone));
            }

            return [
                'id' => $entity->getId(),
                'text' => $text,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
