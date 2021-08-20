<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\CustomerView;
use App\EasyAdmin\Controller\AbstractController;
use App\Order\Entity\Order;
use Doctrine\ORM\Query\Expr\Join;
use EasyCorp\Bundle\EasyAdminBundle\Search\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function array_merge;
use function explode;
use function mb_strtolower;
use function sprintf;
use function ucfirst;

final class CustomerController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function indexAction(Request $request): Response
    {
        if ('autocomplete' === $request->query->get('action')) {
            return parent::indexAction($request);
        }

        $this->initialize($request);
        $id = $request->query->get('id');

        $customer = $this->registry->get(CustomerView::class, $id);

        return $this->redirectToRoute('easyadmin', array_merge($request->query->all(), [
            'entity' => ucfirst($customer->type),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->registry->repository(CustomerView::class)->createQueryBuilder('customer');

        $carId = $query->get('car_id');

        if (null !== $carId) {
            $qb
                ->leftJoin(Order::class, 'o', Join::WITH, 'o.customerId = customer.id')
                ->andWhere('o.carId = :car')
                ->setParameter('car', $carId)
            ;
        }

        $search = $query->has('query') ? explode(' ', (string) $query->get('query')) : [];
        foreach ($search as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(customer.fullName)', $key),
                $qb->expr()->like('LOWER(customer.telephone)', $key),
                $qb->expr()->like('LOWER(customer.email)', $key),
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
        }

        $paginator = $this->get(Paginator::class)->createOrmPaginator($qb, $query->getInt('page', 1));

        $data = array_map(function (CustomerView $entity): array {
            $text = $entity->fullName;

            $telephone = $entity->telephone;

            if (null !== $telephone) {
                $text .= sprintf(' (%s)', $this->formatTelephone($telephone));
            }

            return [
                'id' => $entity->toId()->toString(),
                'text' => $text,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
