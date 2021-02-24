<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Car\Entity\Car;
use App\Customer\Form\SellerType;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use App\Order\Entity\OrderItemPart;
use App\Order\Exception\ReservationException;
use App\Order\Form\Accompanying\AccompanyingDto;
use App\Order\Form\Accompanying\AccompanyingType;
use App\Order\Form\OrderPart;
use App\Order\Form\Type\OrderItemParentType;
use App\Order\Form\Type\WarrantyType;
use App\Order\Manager\ReservationManager;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Form\PartOfferDto;
use App\Part\Form\PartOfferType;
use App\Vehicle\Entity\VehicleId;
use LogicException;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function array_map;
use function sprintf;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrderItemPartController extends OrderItemController
{
    private ReservationManager $reservationManager;

    public function __construct(ReservationManager $reservationManager)
    {
        $this->reservationManager = $reservationManager;
    }

    public function accompanyingAction(): Response
    {
        /** @var OrderItemPart $orderItemPart */
        $orderItemPart = $this->getEntity(OrderItemPart::class);
        $partId = $orderItemPart->getPartId();

        $parts = $this->registry->connection()
            ->fetchAllAssociative(
                <<<'SQL'
                SELECT part_id,
                       COUNT(part_id) AS usage_count,
                       MAX(quantity) AS quantity,
                       MAX(price_amount) AS amount
                FROM order_item_part
                         JOIN order_item oi ON oi.id = order_item_part.id
                WHERE oi.parent_id IN (SELECT oi.parent_id
                                       FROM order_item_part oip
                                                JOIN order_item oi ON oi.id = oip.id
                                       WHERE oip.part_id = :partId
                                            AND oi.parent_id IS NOT NULL
                )
                AND part_id <> :partId
                GROUP BY part_id
                HAVING COUNT(part_id) >= 2
                ORDER BY COUNT(part_id) DESC
                SQL,
                [
                    'partId' => $partId->toString(),
                ]
            )
        ;

        if ([] === $parts) {
            $this->addFlash('info', sprintf('Для запчасти "%s" не найдены сопутствующие запчасти.', $this->display($partId)));

            return $this->redirectToReferrer();
        }

        $parts = array_map(
            static fn (array $item): AccompanyingDto => new AccompanyingDto(
                PartId::fromString($item['part_id']),
                $item['quantity'],
                $item['usage_count'],
                new Money($item['amount'], new Currency('RUB')),
            ),
            $parts,
        );

        $form = $this->createFormBuilder(['parts' => $parts])
            ->add('parts', CollectionType::class, [
                'entry_type' => AccompanyingType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'label' => null,
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $entities = [];

            foreach ($parts as $accompanyingDto) {
                if (false === $accompanyingDto->enabled) {
                    continue;
                }

                $entity = new OrderItemPart(
                    Uuid::uuid6(),
                    $orderItemPart->getOrder(),
                    $accompanyingDto->partId,
                    $accompanyingDto->quantity,
                );
                $entity->setPrice(
                    $accompanyingDto->price,
                    $this->registry->get(PartView::class, $accompanyingDto->partId),
                );
                $entity->setParent($orderItemPart->getParent());

                $entities[] = $entity;
            }

            $this->registry->add(...$entities);

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order/accompanying.html.twig', [
            'partId' => $partId,
            'form' => $form->createView(),
        ]);
    }

    public function reserveAction(): Response
    {
        $item = $this->getEntity(OrderItemPart::class);

        if (!$item instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }

        try {
            $this->reservationManager->reserve($item);
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToReferrer();
    }

    public function deReserveAction(): Response
    {
        $item = $this->getEntity(OrderItemPart::class);

        if (!$item instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }

        try {
            $this->reservationManager->deReserve($item, $item->getQuantity());
        } catch (ReservationException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToReferrer();
    }

    protected function newAction(): Response
    {
        $order = $this->getEntity(Order::class);

        if (!$order instanceof Order) {
            throw new BadRequestHttpException('Order not found');
        }

        if (!$order->isEditable()) {
            throw new BadRequestHttpException('Order closed.');
        }

        $partOffer = new PartOfferDto();
        $dto = new OrderPart();
        $dto->order = $order;
        $dto->partOffer = $partOffer;

        // TODO OrderItemParentType fetch current item from RequestStack
        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $dto;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $partId = $this->getIdentifier(PartId::class);

        if ($partId instanceof PartId) {
            $partOffer->partId = $partId;
        }

        $parent = $this->getEntity(OrderItem::class);

        if ($parent instanceof OrderItem) {
            $dto->parent = $parent;
        }

        $vehicleId = null;
        $carId = $order->getCarId();

        if (null !== $carId) {
            $car = $this->registry->get(Car::class, $carId);
            $vehicleId = $car->vehicleId;
        }

        $form = $this->createItemForm($dto, $vehicleId, 'new')
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $orderItemPart = new OrderItemPart(
                Uuid::uuid6(),
                $order,
                $partOffer->partId,
                $partOffer->quantity
            );
            $orderItemPart->setParent($dto->parent);
            $orderItemPart->setWarranty($dto->warranty);
            $orderItemPart->setSupplierId($dto->supplierId);
            $orderItemPart->setPrice(
                $partOffer->price,
                $this->registry->get(PartView::class, $partOffer->partId),
            );

            $em->persist($orderItemPart);
            $em->flush();

            try {
                $this->reservationManager->reserve($orderItemPart);
            } catch (ReservationException $e) {
                $this->addFlash('warning', $e->getMessage());
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order_item_part/new.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    protected function editAction(): Response
    {
        $entity = $this->findCurrentEntity();

        if (!$entity instanceof OrderItemPart) {
            throw new LogicException('OrderItemPart required.');
        }
        $order = $entity->getOrder();

        if (!$order->isEditable()) {
            throw new BadRequestHttpException('Order closed.');
        }

        $price = $entity->getPrice();
        $discount = $entity->discount();

        if ($discount->isPositive()) {
            $price = $price->subtract($discount);
        }

        $partOffer = new PartOfferDto();
        $partOffer->partId = $entity->getPartId();
        $partOffer->quantity = $entity->getQuantity();
        $partOffer->price = $price;

        $dto = new OrderPart();
        $dto->order = $order;
        $dto->parent = $entity->getParent();
        $dto->partOffer = $partOffer;
        $dto->warranty = $entity->isWarranty();
        $dto->supplierId = $entity->getSupplierId();

        $vehicleId = null;
        $carId = $order->getCarId();

        if (null !== $carId) {
            $car = $this->registry->get(Car::class, $carId);
            $vehicleId = $car->vehicleId;
        }

        $form = $this->createItemForm($dto, $vehicleId, 'edit')
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $entity->setParent($dto->parent);
            $entity->setPrice($partOffer->price, $this->registry->get(PartView::class, $partOffer->partId));
            $entity->setQuantity($partOffer->quantity);
            $entity->setWarranty($dto->warranty);
            $entity->setSupplierId($dto->supplierId);

            $em->flush();

            try {
                $this->reservationManager->reserve($entity);
            } catch (ReservationException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/order_item_part/edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'delete_form' => $this->createDeleteForm($this->entity['name'], $entity->toId()->toString())->createView(),
        ]);
    }

    private function createItemForm(OrderPart $dto, ?VehicleId $vehicleId, string $view): FormInterface
    {
        return $this->createFormBuilder($dto, [
            'attr' => [
                'class' => $view.'-form',
            ],
        ])
            ->add('parent', OrderItemParentType::class, [
                'label' => 'Работа / Группа',
                'required' => false,
                'placeholder' => 'Не выбрано',
            ])
            ->add('partOffer', PartOfferType::class, [
                'vehicleId' => $vehicleId,
            ])
            ->add('warranty', WarrantyType::class)
            ->add('supplierId', SellerType::class, [
                'required' => false,
            ])
            ->getForm()
        ;
    }
}
