<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\Operand;
use App\Entity\OperandNote;
use App\Entity\Order;
use App\Entity\Organization;
use App\Entity\Payment;
use App\Entity\Person;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class OperandController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function indexAction(Request $request): Response
    {
        if (self::class !== static::class || 'autocomplete' === $request->query->get('action')) {
            return parent::indexAction($request);
        }

        $id = $request->query->get('id');
        $entity = $this->getDoctrine()->getManager()->getRepository(Operand::class)->find($id);
        $class = ClassUtils::getClass($entity);
        $config = $this->get('easyadmin.config.manager')->getEntityConfigByClass($class);

        return $this->redirectToRoute('easyadmin', array_merge($request->query->all(), [
            'entity' => $config['name'],
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $em = $this->em;

            $operand = $parameters['entity'];

            $parameters['cars'] = $em->getRepository(Car::class)
                ->findBy(['owner' => $operand]);
            $parameters['orders'] = $em->getRepository(Order::class)
                ->findBy(['customer' => $operand], ['closedAt' => 'DESC'], 20);
            $parameters['payments'] = $em->getRepository(Payment::class)
                ->findBy(['recipient' => $operand], ['id' => 'DESC'], 20);
            $parameters['notes'] = $em->getRepository(OperandNote::class)
                ->findBy(['operand' => $operand], ['createdAt' => 'DESC']);

            $amount = $em->createQueryBuilder()
                ->select('SUM(payment.amount)')
                ->from(Payment::class, 'payment')
                ->where('payment.recipient = :recipient')
                ->setParameter('recipient', $operand)
                ->getQuery()->getSingleScalarResult();
            $parameters['balance'] = new Money($amount, new Currency('RUB'));
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
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
        $qb = $this->em->getRepository(Operand::class)->createQueryBuilder('operand')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = operand.id AND operand INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = operand.id AND operand INSTANCE OF '.Organization::class);

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Operand $entity) {
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
