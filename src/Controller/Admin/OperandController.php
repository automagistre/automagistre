<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Operand;
use App\Entity\Organization;
use App\Entity\Person;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Query\Expr\Join;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OperandController extends AdminController
{
    public function indexAction(Request $request)
    {
        if ('autocomplete' === $request->query->get('action')) {
            return parent::indexAction($request);
        }

        $id = $request->query->get('id');
        $entity = $this->getDoctrine()->getManager()->getRepository(Operand::class)->findOneBy(['id' => $id]);
        $class = ClassUtils::getRealClass(get_class($entity));
        $config = $this->get('easyadmin.config.manager')->getEntityConfigByClass($class);

        return $this->redirectToRoute('easyadmin', array_merge($request->query->all(), [
            'entity' => $config['name'],
        ]));
    }

    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
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

    protected function autocompleteAction()
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder($query->get('entity'), $query->get('query'), []);

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = array_map(function (Operand $entity) {
            return [
                'id'   => $entity->getId(),
                'text' => $entity->getFullName(),
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
