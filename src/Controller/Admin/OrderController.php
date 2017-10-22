<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Operand;
use App\Entity\Order;
use App\Entity\OrderPayment;
use App\Entity\Organization;
use App\Entity\Payment;
use App\Entity\Person;
use App\Form\Type\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Money\Money;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderController extends AdminController
{
    public function isActionAllowed($actionName): bool
    {
        if ('show' !== $actionName && $id = $this->request->get('id')) {
            $entity = $this->em->getRepository(Order::class)->find($id);

            return $entity->isEditable();
        }

        return parent::isActionAllowed($actionName);
    }

    public function paymentAction(): Response
    {
        $order = $this->getEntity(Order::class);
        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order is required');
        }

        $model = new \App\Form\Model\Payment();
        $model->recipient = $order->getCustomer();
        $model->description = '# Начисление по заказу #'.$order->getId();
        $model->amount = $order->getTotalForPayment();

        $form = $this->createForm(PaymentType::class, $model, [
            'disable_recipient' => true,
        ]);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->transactional(function (EntityManagerInterface $em) use ($model, $order): void {
                $calcSubtotal = function (Operand $recipient, Money $money) use ($em) {
                    /** @var Payment $lastPayment */
                    $lastPayment = $em->createQueryBuilder()
                        ->select('payment')
                        ->from(Payment::class, 'payment')
                        ->where('payment.recipient = :recipient')
                        ->orderBy('payment.id', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('recipient', $recipient)
                        ->getQuery()
                        ->getOneOrNullResult();

                    return $lastPayment->getSubtotal()->add($money);
                };

                if (null !== $model->recipient) {
                    $em->persist(new Payment(
                        $model->recipient,
                        $model->description,
                        $model->amount,
                        $calcSubtotal($model->recipient, $model->amount)
                    ));
                }

                // 1 Касса, 2422 = Безнал
                $cashbox = $em->getRepository(Operand::class)->find('cash' === $model->paymentType ? 1 : 2422);
                $em->persist(
                    $payment = new Payment(
                        $cashbox,
                        $model->description,
                        $model->amount,
                        $calcSubtotal($cashbox, $model->amount)
                    )
                );

                $em->persist(new OrderPayment($order, $payment));
                $em->persist($payment);
            });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/payment.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->em->getRepository(Order::class)->createQueryBuilder('orders')
            ->leftJoin('orders.customer', 'customer')
            ->leftJoin('orders.car', 'car')
            ->leftJoin('car.carModel', 'carModel')
            ->leftJoin('car.carModification', 'carModification')
            ->leftJoin('carModel.manufacturer', 'manufacturer')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = customer.id AND customer INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = customer.id AND customer INSTANCE OF '.Organization::class);

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('car.gosnomer', $key),
                $qb->expr()->like('carModel.name', $key),
                $qb->expr()->like('carModification.name', $key),
                $qb->expr()->like('manufacturer.name', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        return $qb;
    }

    /**
     * @param Order $entity
     */
    protected function prePersistEntity($entity): void
    {
        $this->get('event_dispatcher')->addListener(EasyAdminEvents::POST_PERSIST, function (GenericEvent $event
        ): void {
            /** @var Order $entity */
            $entity = $event->getArgument('entity');

            $this->request->query->set('referer', $this->generateUrl('easyadmin', [
                'entity' => 'Order',
                'action' => 'show',
                'id' => $entity->getId(),
            ]));
        });
    }
}
